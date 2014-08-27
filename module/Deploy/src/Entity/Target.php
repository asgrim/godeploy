<?php

namespace Deploy\Entity;

class Target
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $hostname;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $publicKey;

    /**
     * @var string
     */
    protected $privateKey;

    /**
     * @var string
     */
    protected $directory;

    /**
     * Create a new Target entity from configuration array
     *
     * @param string $name
     * @param array $configuration
     * @return \Deploy\Entity\Target
     */
    public static function createFromConfiguration($name, array $configuration)
    {
        $target = new self();
        $target->name = (string)$name;
        $target->hostname = $configuration['hostname'];
        $target->username = $configuration['username'];
        $target->publicKey = $configuration['public-key'];
        $target->privateKey = $configuration['private-key'];
        $target->directory = $configuration['directory'];

        return $target;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }
}
