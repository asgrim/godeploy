<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Deploy\Service\ProjectService;
use Deploy\Service\TaskService;

class ProjectSettingsController extends AbstractActionController
{
    /**
     * @var \Deploy\Service\ProjectService
     */
    protected $projectService;

    /**
     * @var \Deploy\Service\TaskService
     */
    protected $taskService;

    public function __construct(ProjectService $projectService, TaskService $taskService)
    {
        $this->projectService = $projectService;
        $this->taskService = $taskService;
    }

    public function indexAction()
    {
        $project = $this->projectService->findByName($this->params('project'));

        return [
        	'project' => $project,
        ];
    }

    public function viewTasksAction()
    {
        $project = $this->projectService->findByName($this->params('project'));

        $tasks = $this->taskService->findByProjectId($project->getId());

        return [
            'project' => $project,
            'tasks' => $tasks,
        ];
    }
}
