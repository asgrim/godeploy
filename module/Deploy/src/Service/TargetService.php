<?php

namespace Deploy\Service;

use Deploy\Entity\Target;
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

    public function persist(Target $target)
    {
        if ($target->getId() > 0) {
            return $this->targetMapper->update($target);
        } else {
            return $this->targetMapper->insert($target);
        }
    }

    public function delete(Target $target)
    {
        $this->targetMapper->delete($target);
    }

    /**
     * Find targets for a project
     *
     * @param  int                     $projectId
     * @return \Deploy\Entity\Target[]
     */
    public function findByProjectId($projectId)
    {
        return $this->targetMapper->findByProjectId($projectId);
    }

    /**
     *
     * @param int $targetId
     * @return \Deploy\Entity\Target
     */
    public function findById($targetId)
    {
        return $this->targetMapper->findById($targetId);
    }
}
