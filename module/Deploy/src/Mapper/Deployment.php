<?php

namespace Deploy\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;
use Zend\Stdlib\Hydrator\HydratorInterface as Hydrator;

class Deployment extends AbstractDbMapper
{
    protected $tableName  = 'deployment';

    /**
     * Find a deployment by it's ID
     *
     * @param int $id
     * @return \Deploy\Entity\Deployment
     */
    public function findById($id)
    {
        $select = $this->getSelect()->where(['id' => $id]);
        $deployment = $this->select($select)->current();
        return $deployment;
    }

    public function findByProject($projectId, $orderBy = 'date_added DESC')
    {
        $select = $this->getSelect()->where(['project_id' => $id])->order('date_added DESC');
        $deployments = $this->select($select)->current();
        return $deployments;
    }

    public function insert($entity, $tableName = null, Hydrator $hydrator = null)
    {
        $hydrator = $hydrator ?: $this->getHydrator();
        $result = parent::insert($entity, $tableName, $hydrator);
        $hydrator->hydrate(['deployment_id' => $result->getGeneratedValue()], $entity);
        return $result;
    }

    public function update($entity, $where = null, $tableName = null, Hydrator $hydrator = null)
    {
        if (!$where) {
            $where = ['id' => $entity->getId()];
        }

        return parent::update($entity, $where, $tableName, $hydrator);
    }
}
