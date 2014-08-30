<?php

namespace Deploy\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;

class Task extends AbstractDbMapper
{
    protected $tableName  = 'task';

    /**
     * Find a tasks for a project ID
     *
     * @param int $id
     * @return \Deploy\Entity\Task[]
     */
    public function findByProjectId($projectId)
    {
        $select = $this->getSelect()->where(['project_id' => $projectId]);

        $tasks = [];
        foreach ($this->select($select) as $task) {
            $tasks[] = $task;
        }

        return $tasks;
    }
}
