<?php

namespace Deploy\Service;

use Deploy\Entity\Project;

class ProjectService
{
    /**
     * @var \Deploy\Entity\Project[]
     */
    protected $projects;

    public function __construct(array $projectConfiguration)
    {
        $this->loadFromConfig($projectConfiguration);
    }

    /**
     * Populate the service with configuration
     *
     * @param array $projectConfiguration
     */
    protected function loadFromConfig(array $projectConfiguration)
    {
        foreach ($projectConfiguration as $projectKey => $config) {
            $project = Project::createFromConfiguration($projectKey, $config);
            $this->projects[$projectKey] = $project;
        }
    }

    /**
     * Get a list of all projects
     *
     * @return \Deploy\Entity\Project[]
     */
    public function fetchAll()
    {
        return $this->projects;
    }

    /**
     * Find a project by it's "slug" or name
     *
     * @param string $projectName
     * @return \Deploy\Entity\Project|null
     */
    public function findByName($projectName)
    {
        if (!isset($this->projects[$projectName])) {
            throw new \OutOfBoundsException(sprintf('Could not find a project configured for %s', $projectName));
        }

        return $this->projects[$projectName];
    }
}
