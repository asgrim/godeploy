<?php

namespace Deploy\Service;

use Deploy\Mapper\Project as ProjectMapper;

class ProjectService
{
    /**
     * @var \Deploy\Entity\Project[]
     */
    protected $projects;

    /**
     * @var \Deploy\Mapper\Project
     */
    protected $projectMapper;

    public function __construct(ProjectMapper $projectMapper)
    {
        $this->projectMapper = $projectMapper;
    }

    /**
     * Get a list of all projects
     *
     * @return \Deploy\Entity\Project[]
     */
    public function fetchAll()
    {
        $this->projects = $this->projectMapper->findAll();

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
            $project = $this->projectMapper->findByName($projectName);

            if (!$project) {
                throw new \OutOfBoundsException(sprintf('Could not find a project called %s', $projectName));
            }
            $this->projects[$project->getName()] = $project;
        }

        return $this->projects[$projectName];
    }

    /**
     * Find a project by it's "slug" or name
     *
     * @param string $projectName
     * @return \Deploy\Entity\Project|null
     */
    public function findById($id)
    {
        return $this->projectMapper->findById($id);
    }
}
