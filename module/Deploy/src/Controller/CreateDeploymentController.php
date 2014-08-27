<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Deploy\Service\ProjectService;
use Deploy\Service\DeployService;

class CreateDeploymentController extends AbstractActionController
{
    /**
     * @var \Deploy\Service\ProjectService
     */
    protected $projectService;

    /**
     * @var \Deploy\Service\DeployService
     */
    protected $deployService;

    public function __construct(ProjectService $projectService, DeployService $deployService)
    {
        $this->projectService = $projectService;
        $this->deployService = $deployService;
    }

    public function indexAction()
    {
        $project = $this->projectService->findByName($this->params('project'));

        $output = $this->deployService->deploy($project);

        return [
            'project' => $project,
            'output' => $output,
        ];
    }
}
