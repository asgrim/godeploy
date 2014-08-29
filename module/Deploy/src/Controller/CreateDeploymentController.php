<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Deploy\Service\ProjectService;
use Deploy\Service\DeploymentService;
use Deploy\Form\Deployment as DeploymentForm;
use Deploy\Entity\Deployment as DeploymentEntity;

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

    public function __construct(ProjectService $projectService, DeploymentService $deploymentService)
    {
        $this->projectService = $projectService;
        $this->deploymentService = $deploymentService;
    }

    public function indexAction()
    {
        $project = $this->projectService->findByName($this->params('project'));

        $deployment = new DeploymentEntity();
        $deployment->setUserId($this->zfcUserAuthentication()->getIdentity()->getId());
        $deployment->setProjectId($project->getId());
        $deployment->setStatus('PREVIEW');

        $form = new DeploymentForm();
        $form->bind($deployment);

        $request = $this->getRequest();
        if($this->getRequest()->isPost())
        {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid())
            {
                $this->deploymentService->persist($deployment);

                if ($deployment->getId() > 0)
                {
                    return $this->redirect()->toRoute('show-deployment', ['project' => $project->getName(), 'deployment' => $deployment->getId()]);
                }
                else
                {
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
