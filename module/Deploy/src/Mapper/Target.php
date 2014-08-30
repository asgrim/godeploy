<?php

namespace Deploy\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;

class Target extends AbstractDbMapper
{
    protected $tableName  = 'target';

    /**
     * Find a targets for a project ID
     *
     * @param int $id
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
}
