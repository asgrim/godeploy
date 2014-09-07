<?php

namespace Deploy\Service;

use Deploy\Mapper\AdditionalFile as AdditionalFileMapper;

class AdditionalFileService
{
    /**
     * @var \Deploy\Mapper\AdditionalFile
     */
    protected $additionalFileMapper;

    public function __construct(AdditionalFileMapper $additionalFileMapper)
    {
        $this->additionalFileMapper = $additionalFileMapper;
    }

    /**
     * Find Additional Files for a project
     *
     * @param int $projectId
     * @return \Deploy\Entity\AdditionalFile[]
     */
    public function findByProjectId($projectId)
    {
        return $this->additionalFileMapper->findByProjectId($projectId);
    }
}
