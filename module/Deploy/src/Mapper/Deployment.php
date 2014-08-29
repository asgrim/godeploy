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
        $project = $this->select($select)->current();
        return $project;
    }

    public function insert($entity, $tableName = null, Hydrator $hydrator = null)
    {
        $hydrator = $hydrator ?: $this->getHydrator();
        $result = parent::insert($entity, $tableName, $hydrator);
        $hydrator->hydrate(array('deployment_id' => $result->getGeneratedValue()), $entity);
        return $result;
    }
}
