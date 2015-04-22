<?php

namespace Deploy\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;

class Task extends AbstractDbMapper
{
    protected $tableName  = 'task';

    /**
     * Find a tasks for a project ID
     *
     * @param  int                   $id
     * @return \Deploy\Entity\Task[]
     */
    public function findByProjectId($projectId, $orderBy = 'order ASC')
    {
        $select = $this->getSelect()->where(['project_id' => $projectId])->order($orderBy);

        $tasks = [];
        foreach ($this->select($select) as $task) {
            $tasks[] = $task;
        }

        return $tasks;
    }

    public function findById($taskId)
    {
        $select = $this->getSelect()->where(['id' => $taskId]);
        $task = $this->select($select)->current();

        return $task;
    }

    public function insert($entity, $tableName = null, Hydrator $hydrator = null)
    {
        $hydrator = $hydrator ?: $this->getHydrator();
        $result = parent::insert($entity, $tableName, $hydrator);
        $hydrator->hydrate(['id' => $result->getGeneratedValue()], $entity);

        return $result;
    }

    public function update($entity, $where = null, $tableName = null, Hydrator $hydrator = null)
    {
        if (!$where) {
            $where = ['id' => $entity->getId()];
        }

        return parent::update($entity, $where, $tableName, $hydrator);
    }

    public function delete($entity)
    {
        if (empty($entity->getId()) || $entity->getId() <= 0) {
            throw new \InvalidArgumentException('The entity did not have a valid ID');
        }
        $where = ['id' => $entity->getId()];

        return parent::delete($where);
    }
}
