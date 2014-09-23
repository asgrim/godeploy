<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Deploy\Service\ProjectService;
use Deploy\Service\DeploymentService;
use Deploy\Service\DeploymentLogService;
use Deploy\Git\GitRepository;
use Deploy\Service\UserService;

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

    /**
     * @var \Deploy\Git\GitRepository
     */
    protected $gitRepository;

    /**
     * @var \Deploy\Service\UserService
     */
    protected $userService;

    public function __construct(
        ProjectService $projectService,
        DeploymentService $deploymentService,
        DeploymentLogService $deploymentLogService,
        GitRepository $gitRepository,
        UserService $userService
    ) {
        $this->projectService = $projectService;
        $this->deploymentService = $deploymentService;
        $this->deploymentLogService = $deploymentLogService;
        $this->gitRepository = $gitRepository;
        $this->userService = $userService;
    }

    public function indexAction()
    {
        $deploymentId = (int) $this->params('deployment');
        $deployment = $this->deploymentService->findById($deploymentId);
        if (!$deployment) {
            throw new \InvalidArgumentException('Deployment #' . $deploymentId . ' was not found');
        }

        $project = $this->projectService->findById($deployment->getProjectId());

        $user = $this->userService->findById($deployment->getUserId());

        $this->gitRepository->setGitUrl($project->getGitUrl());
        $commitList = $this->gitRepository->getCommitsBetween($deployment->getPreviousRevision(), $deployment->getResolvedRevision());

        $log = $this->deploymentLogService->findById($deployment->getId());

        return [
            'project' => $project,
            'deployment' => $deployment,
            'log' => $log,
            'commitList' => $commitList,
            'user' => $user,
        ];
    }
}
