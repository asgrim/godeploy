<?php

namespace Deploy\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;

class AdditionalFile extends AbstractDbMapper
{
    protected $tableName  = 'additional_files';

    /**
     * Find a additional files for a project ID
     *
     * @param  int                   $id
     * @return \Deploy\Entity\AdditionalFile[]
     */
    public function findByProjectId($projectId, $orderBy = 'order ASC')
    {
        $select = $this->getSelect()->where(['project_id' => $projectId])->order($orderBy);

        $additionalFiles = [];
        foreach ($this->select($select) as $additionalFile) {
            $additionalFiles[] = $additionalFile;
        }

        return $additionalFiles;
    }
}
