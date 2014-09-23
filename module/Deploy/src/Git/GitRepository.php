<?php

namespace Deploy\Git;

use Deploy\Options\GitOptions;
use Deploy\Options\SshOptions;

class GitRepository
{
    /**
     * @var \Deploy\Options\GitOptions
     */
    protected $gitOptions;

    /**
     * @var \Deploy\Options\SshOptions
     */
    protected $sshOptions;

    /**
     * @var string
     */
    protected $gitUrl;

    /**
     * @var string
     */
    protected $gitUrlHash;

    /**
     * @var string
     */
    protected $gitDirectory;

    protected $gitCommand = '/usr/bin/git';

    public function __construct(GitOptions $gitOptions, SshOptions $sshOptions, $gitUrl = null)
    {
        $this->gitOptions = $gitOptions;
        $this->sshOptions = $sshOptions;

        if (!empty($gitUrl)) {
            $this->setGitUrl($gitUrl);
        }
    }

    public function setGitUrl($gitUrl)
    {
        if (empty($gitUrl)) {
            throw new \InvalidArgumentException('No git URL set');
        }

        $this->gitUrl = $gitUrl;
        $this->gitUrlHash = md5($this->gitUrl);
        $this->gitDirectory = $this->gitOptions->getCacheDirectory() . '/' . $this->gitUrlHash . '/';
    }

    public function checkout($revision)
    {
        $revision = $this->sanitiseReference($revision);

        if (empty($revision)) {
            throw new \InvalidArgumentException('Could not resolve an empty revision');
        }

        $output = $this->shell($this->gitCommand . ' checkout ' . $revision);

        if ($this->getLastErrorNumber() == 0) {
            return true;
        } else {
            throw new \RuntimeException('Failed to check out revision [' . $revision . ']: ' . implode(' // ', $output));
        }
    }

    public function getCurrentHead()
    {
        $output = $this->shell($this->gitCommand . ' rev-parse HEAD');

        if ($this->getLastErrorNumber() == 0) {
            return $output[0];
        } else {
            throw new \RuntimeException('Failed to get current head: ' . implode(' // ', $output));
        }
    }

    public function getLatestCommit()
    {
        $output = $this->shell($this->gitCommand . ' log -n1 --format="format:%H" origin/master');

        if ($this->getLastErrorNumber() == 0) {
            return $output[0];
        } else {
            throw new \RuntimeException('Failed to get latest commit: ' . implode(' // ', $output));
        }
    }

    public function resolveRevision($revision)
    {
        $revision = $this->sanitiseReference($revision);

        if (empty($revision)) {
            throw new \InvalidArgumentException('Could not resolve an empty revision');
        }

        $output = $this->shell($this->gitCommand . ' log -n1 --format="format:%H" ' . escapeshellarg($revision));

        if ($this->getLastErrorNumber() == 0) {
            return trim($output[0]);
        } else {
            throw new \RuntimeException('Failed to resolve revision ' . $revision);
        }
    }

    public function update()
    {
        if ($this->isRepositoryValid()) {
            $this->performFetch();
        } else {
            $this->performClone();
        }
    }

    private function performFetch()
    {
        $this->prepareGitCommand();
        $this->shell($this->gitCommand . ' fetch origin');

        if ($this->getLastErrorNumber() == 0) {
            $this->shell($this->gitCommand . ' fetch --tags origin');

            if ($this->getLastErrorNumber() == 0) {
                return true;
            } else {
                throw new \RuntimeException('Failed to fetch tags from origin');
            }
        } else {
            throw new \RuntimeException('Failed to fetch from origin');
        }
    }

    private function performClone()
    {
        $this->prepareGitCommand();
        $output = $this->shell($this->gitCommand . ' clone ' . escapeshellarg($this->gitUrl) . ' ' . escapeshellarg($this->gitDirectory));

        if ($this->getLastErrorNumber() == 0) {
            return true;
        } else {
            $implodedOutput = implode(' // ', $output);

            if (stripos($implodedOutput, 'already exists and is not an empty directory') !== false) {
                throw new \RuntimeException('Destination git path already exists and is not an empty directory');
            }

            if (stripos($implodedOutput, 'Initialized empty Git repository in') !== false
                && stripos($implodedOutput, 'Host key verification failed.') !== false) {
                throw new \RuntimeException('Host key verification failed');
            }

            if (stripos($implodedOutput, 'fatal: remote error') !== false) {
                if (stripos($implodedOutput, 'Count not find repository') !== false) {
                    throw new \RuntimeException('Could not find remote repository');
                } else {
                    throw new \RuntimeException('Could not clone - remote error');
                }
            }

            if (stripos($implodedOutput, 'WARNING: UNPROTECTED PRIVATE KEY FILE!') !== false) {
                throw new \RuntimeException('File permissions on the private key are too open');
            }

            if (stripos($implodedOutput, 'Permission denied (publickey)') !== false) {
                throw new \RuntimeException('Server rejected public key');
            }

            throw new \RuntimeException('Failed to clone: ' . $implodedOutput);
        }
    }

    private function isRepositoryValid()
    {
        $dotGitDirectory = $this->gitDirectory . '.git';
        if (!file_exists($dotGitDirectory)) {
            return false;
        }

        $this->shell($this->gitCommand . ' status');
        return ($this->getLastErrorNumber() == 0);
    }

    private function prepareGitCommand()
    {
        if ($this->parseRepoType($this->gitUrl) != 'ssh') {
            return true;
        }

        $scriptFilename = $this->gitOptions->getCacheDirectory() . '/ssh_' . $this->gitUrlHash . '.sh';

        if (!file_exists($scriptFilename)) {
            $sshCommand = 'ssh -T -o StrictHostKeyChecking=no -i ' . escapeshellarg(realpath($this->sshOptions->getPrivateKey())) . ' -o  UserKnownHostsFile=/dev/null ';

            // Use a script file
            $scriptContent = "#!/bin/sh\n\n{$sshCommand} $*\n";
            file_put_contents($scriptFilename, $scriptContent);
            chmod($scriptFilename, 0755);
        }

        putenv("GIT_SSH={$scriptFilename}");

        // Test the connection
        $x = strrchr($this->gitUrl, ':');
        $host = substr($this->gitUrl, 0, -strlen($x));
        $host = preg_replace("/[^@0-9a-zA-Z-_.]/", "", $host);
        $output = $this->shell("\$GIT_SSH -T -o StrictHostKeyChecking=no {$host}");

        if($this->getLastErrorNumber() != 0)
        {
            // First check if we're a Github sort of repo
            // Github returns: Hi [USER]! You've successfully authenticated, but GitHub does not provide shell access.
            // Codebase returns: You've successfully uploaded your public key to Codebase and authenticated.
            // Beanstalk returns: You were successfully authenticated as <user email> in <host>.
            $validString = "#You('ve| were) successfully#";

            foreach($output as $line) {
                if(preg_match($validString, $line) !== false) {
                    return true;
                }
            }

            if(in_array("ERROR:gitosis.serve.main:Need SSH_ORIGINAL_COMMAND in environment.", $output)) {
                return true;
            } else if(strpos($output[0], "Could not resolve hostname") !== false
                || (isset($output[1]) && strpos($output[1], "Could not resolve hostname") !== false)) {
                throw new \RuntimeException("Could not resolve hostname '{$host}'");
            } else {
                $final_error = end($this->output);
                throw new \RuntimeException("Tried setting up SSH authentication but failed. Final error was: {$final_error}", 0, self::GIT_SSH_ERROR_UNKNOWN);
            }
        }

        return false;
    }

    private function parseRepoType($url)
    {
        if (substr($url, 0, 6) == 'git://') {
            return 'git';
        } else if (substr($url, 0, 8) == 'https://') {
            throw new \InvalidArgumentException('HTTPS URLs are not yet supported');
        } else {
            return 'ssh';
        }
    }

    private function shell($command)
    {
        $originalDirectory = getcwd();
        chdir($this->gitDirectory);
        $output = $this->gitOptions->getShell()->execute($command);
        chdir($originalDirectory);
        return $output;
    }

    private function getLastErrorNumber()
    {
        return $this->gitOptions->getShell()->getLastErrorNumber();
    }

    private function sanitiseReference($reference)
    {
        return preg_replace('/[^-\/a-zA-Z0-9_-]/', '', $reference);
    }
}