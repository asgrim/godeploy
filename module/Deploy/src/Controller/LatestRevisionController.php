<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Deploy\Service\ProjectService;
use Deploy\Git\GitRepository;

class LatestRevisionController extends AbstractActionController
{
    /**
     * @var \Deploy\Service\ProjectService
     */
    protected $projectService;

    /**
     * @var \Deploy\Git\GitRepository
     */
    protected $gitRepository;

    public function __construct(ProjectService $projectService, GitRepository $gitRepository)
    {
        $this->projectService = $projectService;
        $this->gitRepository = $gitRepository;
    }

    public function indexAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new \Exception("Must be an XML Http Request...");
        }

        $project = $this->projectService->findByName($this->params('project'));
        $this->gitRepository->setGitUrl($project->getGitUrl());

        $currentCommit = $this->gitRepository->getCurrentHead();
        $latestCommit = $this->gitRepository->getLatestCommit();
        $commitList = $this->gitRepository->getCommitsBetween($currentCommit, $latestCommit);

        $commitListHelper = $this->serviceLocator->get('viewhelpermanager')->get('commitList');
        $commitListHtml = $commitListHelper($commitList);

        return new JsonModel([
            'latestCommit' => $latestCommit,
            'commitListHtml' => $commitListHtml,
        ]);
    }
}
