<?php

namespace Deploy\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;
use Zend\Stdlib\Hydrator\HydratorInterface as Hydrator;

class DeploymentLog extends AbstractDbMapper
{
    protected $tableName  = 'deployment_log';

    /**
     * Find a deployment log by it's ID
     *
     * @param int $id
     * @return \Deploy\Entity\DeploymentLog
     */
    public function findById($id)
    {
        $select = $this->getSelect()->where(['deployment_id' => $id]);
        $deploymentLog = $this->select($select)->current();
        return $deploymentLog;
    }

    public function insert($entity, $tableName = null, Hydrator $hydrator = null)
    {
        $hydrator = $hydrator ?: $this->getHydrator();
        $result = parent::insert($entity, $tableName, $hydrator);
         return $result;
    }

    public function update($entity, $where = null, $tableName = null, Hydrator $hydrator = null)
    {
        if (!$where) {
            $where = ['deployment_id' => $entity->getDeploymentId()];
        }

        return parent::update($entity, $where, $tableName, $hydrator);
    }
}
