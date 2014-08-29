<?php

namespace Deploy\Connection;

use Deploy\Entity\Target;
use Deploy\Options\SshOptions;

class SshConnection implements Connectable
{
    /**
     * @var \Deploy\Entity\Target
     */
    protected $target;

    /**
     * @var \Deploy\Options\SshOptions
     */
    protected $sshOptions;

    /**
     * @var resource
     */
    protected $handle;

    public function __construct(Target $target, SshOptions $sshOptions)
    {
        $this->target = $target;
        $this->sshOptions = $sshOptions;
    }

    public function connect()
    {
        $methods = [
            'hostkey' => 'ssh-rsa, ssh-dss',
        ];

        $callbacks = [
            'disconnect' => [$this, 'eventDisconnect'],
        ];

        $this->handle = ssh2_connect($this->target->getHostname(), 22, $methods, $callbacks);

        if (!$this->handle) {
            throw new \Exception("Failed to connect.");
        }

        if (!ssh2_auth_pubkey_file($this->handle, $this->target->getUsername(), $this->sshOptions->getPublicKey(), $this->sshOptions->getPrivateKey())) {
            throw new \Exception("Unable to auth using pubkey");
        }
    }

    public function execute($command, $directory = null)
    {
        $cd = !is_null($directory) ? $directory : $this->target->getDirectory();
        $stream = ssh2_exec($this->handle, 'cd ' . $cd . ';' . $command);

        if (!$stream) {
            throw new \Exception("Unable to execute command: " . $command);
        }

        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
        stream_set_blocking($errorStream, true);
        stream_set_blocking($stream, true);

        $stdout = stream_get_contents($stream);
        $stderr = stream_get_contents($errorStream);

        return [
            'stdout' => $this->makeArrayFromStream($stdout),
            'stderr' => $this->makeArrayFromStream($stderr),
        ];
    }

    protected function makeArrayFromStream($text)
    {
        if (strlen($text) == 0) return [];

        $original = explode("\n", $text);
        $cleaned = [];
        foreach ($original as $line) {
            if (strlen($line) == 0) continue;

            $cleaned[] = $line;
        }
        return $cleaned;
    }

    public function eventDisconnect($reason, $message)
    {
        throw new \Exception(sprintf("Server disconnected with reason code [%d] and message: %s", $reason, $message));
    }

    public function disconnect()
    {
        $this->execute('exit');
        $this->handle = null;
    }

    public function __destruct()
    {
        if ($this->handle) {
            $this->disconnect();
        }
    }
}
