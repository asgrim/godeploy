<?php

namespace Deploy\Service;

use Deploy\Mapper\Target as TargetMapper;

class TargetService
{
    /**
     * @var \Deploy\Mapper\Target
     */
    protected $targetMapper;

    public function __construct(TargetMapper $targetMapper)
    {
        $this->targetMapper = $targetMapper;
    }

    /**
     * Find targets for a project
     *
     * @param int $projectId
     * @return \Deploy\Entity\Target[]
     */
    public function findByProjectId($projectId)
    {
        return $this->targetMapper->findByProjectId($projectId);
    }
}
