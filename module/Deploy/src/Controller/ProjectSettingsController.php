<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Deploy\Service\ProjectService;

class ProjectSettingsController extends AbstractActionController
{
    /**
     * @var \Deploy\Service\ProjectService
     */
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function indexAction()
    {
        $project = $this->projectService->findByName($this->params('project'));

        return [
        	'project' => $project,
        ];
    }
}
