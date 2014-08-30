<?php

namespace Deploy\Service;

use Deploy\Mapper\Task as TaskMapper;

class TaskService
{
    /**
     * @var \Deploy\Mapper\Task
     */
    protected $taskMapper;

    public function __construct(TaskMapper $taskMapper)
    {
        $this->taskMapper = $taskMapper;
    }

    /**
     * Find tasks for a project
     *
     * @param int $projectId
     * @return \Deploy\Entity\Task[]
     */
    public function findByProjectId($projectId)
    {
        return $this->taskMapper->findByProjectId($projectId);
    }
}
