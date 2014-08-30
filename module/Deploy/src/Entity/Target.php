<?php

namespace Deploy\Entity;

class Target
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $projectId;

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
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int
	 * @return \Deploy\Entity\Target
	 */
	public function setId($id)
	{
		$this->id = (int)$id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProjectId()
	{
		return $this->projectId;
	}

	/**
	 * @param int $projectId
	 * @return \Deploy\Entity\Target
	 */
	public function setProjectId($projectId)
	{
		$this->projectId = (int)$projectId;
		return $this;
	}

	/**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

	/**
	 * @param string $name
	 * @return \Deploy\Entity\Target
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

	/**
	 * @param string $hostname
	 * @return \Deploy\Entity\Target
	 */
	public function setHostname($hostname)
	{
		$this->hostname = $hostname;
		return $this;
	}

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

	/**
	 * @param string $username
	 * @return \Deploy\Entity\Target
	 */
	public function setUsername($username)
	{
		$this->username = $username;
		return $this;
	}

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

	/**
	 * @param string $directory
	 * @return \Deploy\Entity\Target
	 */
	public function setDirectory($directory)
	{
		$this->directory = $directory;
		return $this;
	}
}
