<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Deploy\Service\ProjectService;
use Deploy\Git\GitRepository;

class IndexController extends AbstractActionController
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
        $projects = $this->projectService->fetchAll();

        foreach ($projects as $project) {
            $this->gitRepository->setGitUrl($project->getGitUrl());

            try {
                $project->currentCommit = $this->gitRepository->getCurrentHead();
                $project->latestCommit = $this->gitRepository->getLatestCommit();

                if ($project->currentCommit == $project->latestCommit) {
                    $project->isStale = false;
                } else {
                    $project->isStale = true;
                }
            } catch (\Exception $exception) {
                $project->isStale = false;
                $project->currentCommit = 'unknown';
                $project->latestCommit = 'unknown';
            }
        }

        return [
            'projects' => $projects,
        ];
    }
}
