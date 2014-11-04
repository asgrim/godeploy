<?php

namespace Deploy\Service;

use Deploy\Mapper\Task as TaskMapper;
use Deploy\Entity\Task;

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

    public function persist(Task $task)
    {
        if ($task->getId() > 0) {
            return $this->taskMapper->update($task);
        } else {
            return $this->taskMapper->insert($task);
        }
    }

    /**
     * Find tasks for a project
     *
     * @param  int                   $projectId
     * @return \Deploy\Entity\Task[]
     */
    public function findByProjectId($projectId)
    {
        return $this->taskMapper->findByProjectId($projectId);
    }

    /**
     *
     * @param int $taskId
     * @return \Deploy\Entity\Task
     */
    public function findById($taskId)
    {
        return $this->taskMapper->findById($taskId);
    }
}
