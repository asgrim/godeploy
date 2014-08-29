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
    public function getDirectory()
    {
        return $this->directory;
    }
}
