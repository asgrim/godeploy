<?php

namespace Deploy\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;

class Target extends AbstractDbMapper
{
    protected $tableName  = 'target';

    /**
     * Find a targets for a project ID
     *
     * @param  int                     $id
     * @return \Deploy\Entity\Target[]
     */
    public function findByProjectId($projectId)
    {
        $select = $this->getSelect()->where(['project_id' => $projectId]);

        $targets = [];
        foreach ($this->select($select) as $target) {
            $targets[] = $target;
        }

        return $targets;
    }

    public function findById($targetId)
    {
        $select = $this->getSelect()->where(['id' => $targetId]);
        $target = $this->select($select)->current();

        return $target;
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
