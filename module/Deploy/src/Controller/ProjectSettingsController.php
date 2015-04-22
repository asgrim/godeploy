<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Deploy\Service\ProjectService;
use Deploy\Service\TaskService;
use Deploy\Service\TargetService;
use Deploy\Service\AdditionalFileService;
use Deploy\Form\Task as TaskForm;
use Deploy\Entity\Task as TaskEntity;
use Deploy\Form\Target as TargetForm;
use Deploy\Entity\Target as TargetEntity;

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

    /**
     * @var \Deploy\Service\TargetService
     */
    protected $targetService;

    /**
     * @var \Deploy\Service\AdditionalFileService
     */
    protected $additionalFileService;

    public function __construct(
        ProjectService $projectService,
        TaskService $taskService,
        TargetService $targetService,
        AdditionalFileService $additionalFileService
    ) {
        $this->projectService = $projectService;
        $this->taskService = $taskService;
        $this->targetService = $targetService;
        $this->additionalFileService = $additionalFileService;
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

    public function editTaskAction()
    {
        $project = $this->projectService->findByName($this->params('project'));

        $taskId = (int)$this->params('objectId');
        if ($taskId > 0) {
            $task = $this->taskService->findById($taskId);
            $breadcrumb = "Edit Task #" . $task->getId();
        } else {
            $tasks = $this->taskService->findByProjectId($project->getId());
            $lastTask = end($tasks);

            $task = new TaskEntity();
            $task->setProjectId($project->getId());
            $task->setOrder($lastTask->getOrder() + 1);
            $breadcrumb = "Add Task";
        }

        $form = new TaskForm();
        $form->bind($task);

        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->taskService->persist($task);
                $routeOptions = ['project' => $project->getName(), 'action' => 'view-tasks'];
                return $this->redirect()->toRoute('project-settings', $routeOptions);
            }
        }

        return [
            'project' => $project,
            'task' => $task,
            'form' => $form,
            'breadcrumb' => $breadcrumb,
        ];
    }

    public function deleteTaskAction()
    {
        $project = $this->projectService->findByName($this->params('project'));

        $taskId = (int)$this->params('objectId');
        $task = $this->taskService->findById($taskId);

        if ($this->getRequest()->isPost()) {
            $this->taskService->delete($task);
            $routeOptions = ['project' => $project->getName(), 'action' => 'view-tasks'];
            return $this->redirect()->toRoute('project-settings', $routeOptions);
        }

        return [
            'project' => $project,
            'task' => $task,
        ];
    }

    public function viewTargetsAction()
    {
        $project = $this->projectService->findByName($this->params('project'));

        $targets = $this->targetService->findByProjectId($project->getId());

        return [
            'project' => $project,
            'targets' => $targets,
        ];
    }

    public function editTargetAction()
    {
        $project = $this->projectService->findByName($this->params('project'));

        $targetId = (int)$this->params('objectId');
        if ($targetId > 0) {
            $target = $this->targetService->findById($targetId);
            $breadcrumb = "Edit Target #" . $target->getId();
        } else {
            $target = new TargetEntity();
            $target->setProjectId($project->getId());
            $breadcrumb = "Add Target";
        }

        $form = new TargetForm();
        $form->bind($target);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->targetService->persist($target);
                $routeOptions = ['project' => $project->getName(), 'action' => 'view-targets'];
                return $this->redirect()->toRoute('project-settings', $routeOptions);
            }
        }

        return [
            'project' => $project,
            'target' => $target,
            'form' => $form,
            'breadcrumb' => $breadcrumb,
        ];
    }

    public function deleteTargetAction()
    {
        $project = $this->projectService->findByName($this->params('project'));

        $targetId = (int)$this->params('objectId');
        $target = $this->targetService->findById($targetId);

        if ($this->getRequest()->isPost()) {
            $this->targetService->delete($target);
            $routeOptions = ['project' => $project->getName(), 'action' => 'view-targets'];
            return $this->redirect()->toRoute('project-settings', $routeOptions);
        }

        return [
            'project' => $project,
            'target' => $target,
        ];
    }

    public function viewFilesAction()
    {
        $project = $this->projectService->findByName($this->params('project'));

        $additionalFiles = $this->additionalFileService->findByProjectId($project->getId());

        return [
            'project' => $project,
            'additionalFiles' => $additionalFiles,
        ];
    }
}
