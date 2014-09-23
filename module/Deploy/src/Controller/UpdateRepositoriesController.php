<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;
use Deploy\Service\ProjectService;
use Deploy\Git\GitRepository;

class UpdateRepositoriesController extends AbstractActionController
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
        $request = $this->getRequest();

        if (!($request instanceof ConsoleRequest)) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        $projects = $this->projectService->fetchAll();

        foreach ($projects as $project) {
            $this->gitRepository->setGitUrl($project->getGitUrl());

            echo "Updating " . $project->getName() . ": ";

            try {
                $this->gitRepository->update();
                echo "OK\n";
            } catch (\Exception $exception) {
                echo "Failed: " . $exception->getMessage() . "\n";
            }
        }
    }
}
