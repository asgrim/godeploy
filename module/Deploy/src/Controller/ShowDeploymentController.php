<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Deploy\Service\ProjectService;
use Deploy\Service\DeploymentService;
use Deploy\Service\DeploymentLogService;

class ShowDeploymentController extends AbstractActionController
{
    /**
     * @var \Deploy\Service\ProjectService
     */
    protected $projectService;

    /**
     * @var \Deploy\Service\DeploymentService
     */
    protected $deploymentService;

    /**
     * @var \Deploy\Service\DeploymentLogService
     */
    protected $deploymentLogService;

    public function __construct(
        ProjectService $projectService,
        DeploymentService $deploymentService,
        DeploymentLogService $deploymentLogService
    ) {
        $this->projectService = $projectService;
        $this->deploymentService = $deploymentService;
        $this->deploymentLogService = $deploymentLogService;
    }

    public function indexAction()
    {
        $deploymentId = (int) $this->params('deployment');
        $deployment = $this->deploymentService->findById($deploymentId);
        if (!$deployment) {
            throw new \InvalidArgumentException('Deployment #' . $deploymentId . ' was not found');
        }

        $project = $this->projectService->findById($deployment->getProjectId());

        $log = $this->deploymentLogService->findById($deployment->getId());

        return [
            'project' => $project,
            'deployment' => $deployment,
            'log' => $log,
        ];
    }
}
