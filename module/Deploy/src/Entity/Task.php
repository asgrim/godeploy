<?php

namespace Deploy\Entity;

class Task
{
    /**
     * @var string
     */
    protected $command;

    /**
     * @var string[]
     */
    protected $onlyOn;

    /**
     * @var string[]
     */
    protected $notOn;

    /**
     * @var string
     */
    protected $directory;

    /**
     * Create a new Project entity from configuration array
     *
     * @param string $name
     * @param array $configuration
     * @return \Deploy\Entity\Project
     */
    public static function createFromConfiguration($name, array $configuration)
    {
        $task = new self();
        $task->name = (string)$name;
        $task->command = $configuration['command'];
        $task->onlyOn = isset($configuration['only-on']) ? $configuration['only-on'] : [];
        $task->notOn = isset($configuration['not-on']) ? $configuration['not-on'] : [];
        $task->directory = isset($configuration['directory']) ? $configuration['directory'] : null;

        return $task;
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
    public function getCommand()
    {
        return $this->command;
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

    public function getDirectory()
    {
        return $this->directory;
    }
}
