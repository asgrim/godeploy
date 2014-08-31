<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Deploy\Service\ProjectService;
use Deploy\Service\DeploymentService;
use Deploy\Form\Deployment as DeploymentForm;
use Deploy\Entity\Deployment as DeploymentEntity;

class ViewHistoryController extends AbstractActionController
{
    /**
     * @var \Deploy\Service\ProjectService
     */
    protected $projectService;

    /**
     * @var \Deploy\Service\DeploymentService
     */
    protected $deploymentService;

    public function __construct(ProjectService $projectService, DeploymentService $deploymentService)
    {
        $this->projectService = $projectService;
        $this->deploymentService = $deploymentService;
    }

    public function indexAction()
    {
        $project = $this->projectService->findByName($this->params('project'));

        $deployments = $this->deploymentService->findByProjectId($project->getId());

        return [
            'project' => $project,
            'deployments' => $deployments,
        ];
    }
}
