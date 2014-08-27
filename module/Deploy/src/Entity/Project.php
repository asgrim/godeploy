<?php

namespace Deploy\Entity;

class Project
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $gitUrl;

    /**
     * @var \Deploy\Entity\Target[]
     */
    protected $targets;

    /**
     * @var \Deploy\Entity\Task[]
     */
    protected $tasks;

    /**
     * Create a new Project entity from configuration array
     *
     * @param string $name
     * @param array $configuration
     * @return \Deploy\Entity\Project
     */
    public static function createFromConfiguration($name, array $configuration)
    {
        $project = new self();
        $project->name = (string)$name;
        $project->gitUrl = $configuration['git-url'];

        foreach ($configuration['targets'] as $targetName => $targetConfig) {
            $project->targets[$targetName] = Target::createFromConfiguration($targetName, $targetConfig);
        }

        foreach ($configuration['tasks'] as $taskName => $taskConfig) {
            $project->tasks[$taskName] = Task::createFromConfiguration($taskName, $taskConfig);
        }

        return $project;
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
    public function getGitUrl()
    {
        return $this->gitUrl;
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
