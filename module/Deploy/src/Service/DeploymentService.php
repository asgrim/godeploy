<?php

namespace Deploy\Service;

use Deploy\Mapper\Deployment as DeploymentMapper;
use Deploy\Entity\Deployment;

class DeploymentService
{
    /**
     * @var \Deploy\Mapper\Deployment
     */
    protected $deploymentMapper;

    public function __construct(DeploymentMapper $deploymentMapper)
    {
        $this->deploymentMapper = $deploymentMapper;
    }

    public function persist(Deployment $deployment)
    {
        if ($deployment->getId() > 0)
        {
            return $this->deploymentMapper->update($deployment);
        }
        else
        {
            return $this->deploymentMapper->insert($deployment);
        }
    }

    public function findById($id)
    {
        return $this->deploymentMapper->findById($id);
    }
}
