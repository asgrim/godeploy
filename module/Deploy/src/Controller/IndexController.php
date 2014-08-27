<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Deploy\Service\ProjectService;

class IndexController extends AbstractActionController
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
        $projects = $this->projectService->fetchAll();

        return [
            'projects' => $projects,
        ];
    }
}
