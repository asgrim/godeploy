<?php

namespace Deploy\Service;

use Deploy\Mapper\DeploymentLog as DeploymentLogMapper;
use Deploy\Entity\DeploymentLog;

class DeploymentLogService
{
    /**
     * @var \Deploy\Mapper\DeploymentLog
     */
    protected $deploymentLogMapper;

    public function __construct(DeploymentLogMapper $deploymentLogMapper)
    {
        $this->deploymentLogMapper = $deploymentLogMapper;
    }

    public function persist(DeploymentLog $deploymentLog)
    {
        $exists = $this->findById($deploymentLog->getDeploymentId());

        if ($exists) {
            return $this->deploymentLogMapper->update($deploymentLog);
        } else {
            return $this->deploymentLogMapper->insert($deploymentLog);
        }
    }

    public function findById($id)
    {
        return $this->deploymentLogMapper->findById($id);
    }
}
