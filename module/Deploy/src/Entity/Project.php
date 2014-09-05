<?php

namespace Deploy\Entity;

class Project
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $displayName;

    /**
     * @var string
     */
    protected $gitUrl;

    /**
     * @var \Deploy\Entity\Target[]
     */
    protected $targets = [];

    /**
     * @var \Deploy\Entity\Task[]
     */
    protected $tasks = [];

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @return string
     */
    public function getGitUrl()
    {
        return $this->gitUrl;
    }

    public function setGitUrl($gitUrl)
    {
        $this->gitUrl = $gitUrl;
    }

    /**
     * @var \Deploy\Entity\Target[]
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * @var \Deploy\Entity\Task[]
     */
    public function getTasks()
    {
        return $this->tasks;
    }
}
