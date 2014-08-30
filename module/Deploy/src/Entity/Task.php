<?php

namespace Deploy\Entity;

class Task
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
     * @var int
     */
    protected $order;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string[]
     */
    protected $onlyOnTargets;

    /**
     * @var string[]
     */
    protected $notOnTargets;

    /**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
     * @return \Deploy\Entity\Task
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
     * @return \Deploy\Entity\Task
	 */
	public function setProjectId($projectId)
	{
		$this->projectId = (int)$projectId;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getOrder()
	{
		return $this->order;
	}

	/**
	 * @param int $order
     * @return \Deploy\Entity\Task
	 */
	public function setOrder($order)
	{
		$this->order = (int)$order;
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
     * @return \Deploy\Entity\Task
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

	/**
	 * @param string $command
     * @return \Deploy\Entity\Task
	 */
	public function setCommand($command)
	{
		$this->command = $command;
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
     * @return \Deploy\Entity\Task
	 */
	public function setDirectory($directory)
	{
		$this->directory = $directory;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOnlyOnTargets()
	{
		return $this->onlyOnTargets;
	}

	/**
	 * @param string $onlyOnTargets
     * @return \Deploy\Entity\Task
	 */
	public function setOnlyOnTargets($onlyOnTargets)
	{
		$this->onlyOnTargets = $onlyOnTargets;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getNotOnTargets()
	{
		return $this->notOnTargets;
	}

	/**
	 * @param string $notOnTargets
     * @return \Deploy\Entity\Task
	 */
	public function setNotOnTargets($notOnTargets)
	{
		$this->notOnTargets = $notOnTargets;
		return $this;
	}

    public function allowedOnTarget(Target $target)
    {
        if (count($this->notOn) > 0 && in_array($target->getName(), $this->notOn))
        {
            return false;
        }

        if (count($this->onlyOn) > 0 && !in_array($target->getName(), $this->onlyOn))
        {
            return false;
        }

        return true;
    }
}
