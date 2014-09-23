<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Deploy\Service\ProjectService;
use Deploy\Service\DeploymentService;
use Deploy\Form\Deployment as DeploymentForm;
use Deploy\Entity\Deployment as DeploymentEntity;
use Deploy\Git\GitRepository;

class CreateDeploymentController extends AbstractActionController
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
     * @var \Deploy\Git\GitRepository
     */
    protected $gitRepository;

    public function __construct(
        ProjectService $projectService,
        DeploymentService $deploymentService,
        GitRepository $gitRepository
    ) {
        $this->projectService = $projectService;
        $this->deploymentService = $deploymentService;
        $this->gitRepository = $gitRepository;
    }

    public function indexAction()
    {
        $project = $this->projectService->findByName($this->params('project'));
        $this->gitRepository->setGitUrl($project->getGitUrl());

        $deployment = new DeploymentEntity();
        $deployment->setUserId($this->zfcUserAuthentication()->getIdentity()->getId());
        $deployment->setProjectId($project->getId());
        $deployment->setStatus('PREVIEW');
        $deployment->setPreviousRevision($this->gitRepository->getCurrentHead());

        $form = new DeploymentForm();
        $form->bind($deployment);

        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {

                $deployment->setPreviousRevision($this->gitRepository->getCurrentHead());

                if (!$deployment->hasResolvedRevision()) {
                    $revision = $this->gitRepository->resolveRevision($deployment->getRevision());
                    $deployment->setResolvedRevision($revision);
                }

                $this->deploymentService->persist($deployment);

                if ($deployment->getId() > 0) {
                    $params = [
                        'project' => $project->getName(),
                        'deployment' => $deployment->getId(),
                    ];
                    return $this->redirect()->toRoute('show-deployment', $params);
                } else {
                    throw new \RuntimeException('Tried saving deployment to database, but ID was not found');
                }
            }
        }

        return [
            'project' => $project,
            'form' => $form,
        ];
    }
}
