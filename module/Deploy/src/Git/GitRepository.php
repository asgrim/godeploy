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

    protected $gitCommandPrepared = false;

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
        $this->gitCommandPrepared = false;
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
            throw new \RuntimeException(
                'Failed to check out revision [' . $revision . ']: ' . implode(' // ', $output)
            );
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

    public function getCommitsBetween($ref1, $ref2)
    {
        if (empty($ref1) && empty($ref2)) {
            throw new \InvalidArgumentException('You must provide at least one valid reference');
        }

        if (empty($ref1)) {
            // Return an empty object as there are no commits to list
            $retval = new \stdClass();
            $retval->swapped = false;
            $retval->commits = [];
            return $retval;
        }

        $niceRef1 = $this->resolveRevision($ref1);
        $niceRef2 = $this->resolveRevision($ref2);

        $cmd = $this->gitCommand . ' merge-base ' . escapeshellarg($niceRef1) . ' ' . escapeshellarg($niceRef2);
        $output = $this->shell($cmd);

        if ($this->getLastErrorNumber() == 0) {
            $firstRef = $output[0];

            $retval = new \stdClass();
            $retval->swapped = null;
            $retval->commits = [];

            if ($firstRef == $niceRef1) {
                $retval->swapped = false;
            } elseif ($firstRef == $niceRef2) {
                $retval->swapped = true;

                // Swap them round with XOR. Mind blown = true.
                $niceRef1 = $niceRef1 ^ $niceRef2;
                $niceRef2 = $niceRef1 ^ $niceRef2;
                $niceRef1 = $niceRef1 ^ $niceRef2;
            } else {
                throw new \RuntimeException(
                    "Could not tell whether '{$firstRef}' was '{$niceRef1}' or '{$niceRef2}' in getCommitsBetween"
                );
            }

            $cmd = $this->gitCommand . " --no-pager log --pretty=format:'%H,%an,%s' {$niceRef1}..{$niceRef2}";
            $output = $this->shell($cmd);

            if ($this->getLastErrorNumber() == 0) {
                foreach ($output as $line) {
                    $rawCommitInfo = explode(",", $line, 3);
                    $retval->commits[] = [
                        'HASH' => $rawCommitInfo[0],
                        'AUTHOR' => $rawCommitInfo[1],
                        'MESSAGE' => $rawCommitInfo[2],
                    ];
                }

                return $retval;
            } else {
                throw new \RuntimeException("Could not git log for commits between '{$niceRef1}' and '{$niceRef2}'");
            }
        } else {
            throw new \RuntimeException("Could not determine merge-base for commits '{$niceRef1}' and '{$niceRef2}'");
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
        $output = $this->shell($this->gitCommand . ' fetch origin');

        if ($this->getLastErrorNumber() == 0) {
            $output = $this->shell($this->gitCommand . ' fetch --tags origin');

            if ($this->getLastErrorNumber() == 0) {
                return true;
            } else {
                throw new \RuntimeException('Failed to fetch tags from origin: ' . implode(' // ', $output));
            }
        } else {
            throw new \RuntimeException('Failed to fetch from origin: ' . implode(' // ', $output));
        }
    }

    private function performClone()
    {
        $this->prepareGitCommand();

        $cmd = $this->gitCommand . ' clone ';
        $cmd .= escapeshellarg($this->gitUrl) . ' ' . escapeshellarg($this->gitDirectory);
        $output = $this->shell($cmd, false);

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

        if ($this->gitCommandPrepared) {
            return true;
        }

        $scriptFilename = $this->gitOptions->getCacheDirectory() . '/ssh_' . $this->gitUrlHash . '.sh';

        if (!file_exists($scriptFilename)) {
            $sshCommand = 'ssh -T -o StrictHostKeyChecking=no -i ';
            $sshCommand .= escapeshellarg(realpath($this->sshOptions->getPrivateKey()));
            $sshCommand .= ' -o  UserKnownHostsFile=/dev/null ';
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
        $output = $this->shell("\$GIT_SSH -T -o StrictHostKeyChecking=no {$host}", false);


        if ($this->getLastErrorNumber() != 0) {
            // First check if we're a Github sort of repo
            // Github returns: Hi [USER]! You've successfully authenticated, but GitHub does not provide shell access.
            // Codebase returns: You've successfully uploaded your public key to Codebase and authenticated.
            // Beanstalk returns: You were successfully authenticated as <user email> in <host>.
            $validString = "#You('ve| were) successfully#";

            foreach ($output as $line) {
                if (preg_match($validString, $line) !== false) {
                    $this->gitCommandPrepared = true;
                    return true;
                }
            }

            if (in_array("ERROR:gitosis.serve.main:Need SSH_ORIGINAL_COMMAND in environment.", $output)) {
                $this->gitCommandPrepared = true;
                return true;
            } elseif (strpos($output[0], "Could not resolve hostname") !== false
                || (isset($output[1]) && strpos($output[1], "Could not resolve hostname") !== false)) {
                throw new \RuntimeException("Could not resolve hostname '{$host}'");
            } else {
                $final_error = end($this->output);
                throw new \RuntimeException(
                    "Tried setting up SSH authentication but failed. Final error was: {$final_error}"
                );
            }
        }

        return false;
    }

    private function parseRepoType($url)
    {
        if (substr($url, 0, 6) == 'git://') {
            return 'git';
        } elseif (substr($url, 0, 8) == 'https://') {
            throw new \InvalidArgumentException('HTTPS URLs are not yet supported');
        } else {
            return 'ssh';
        }
    }

    private function shell($command, $changeDirectory = true)
    {
        if ($changeDirectory) {
            $originalDirectory = getcwd();

            if (!file_exists($this->gitDirectory)) {
                throw new \RuntimeException('Git directory does not exist: ' . $this->gitDirectory);
            }
            chdir($this->gitDirectory);
        }
        $output = $this->gitOptions->getShell()->execute($command);
        if ($changeDirectory) {
            chdir($originalDirectory);
        }
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
