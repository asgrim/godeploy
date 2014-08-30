<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Deploy\Deployer\Deployer;
use Deploy\Service\DeploymentService;
use Deploy\Mapper\DeploymentHydrator;

class RunDeploymentController extends AbstractActionController
{
    /**
     * @var \Deploy\Deployer\Deployer
     */
    protected $deployer;

    /**
     * @var \Deploy\Service\DeploymentService
     */
    protected $deploymentService;

    public function __construct(Deployer $deployer, DeploymentService $deploymentService)
    {
        $this->deployer = $deployer;
        $this->deploymentService = $deploymentService;
    }

    public function indexAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new \Exception("Must be an XML Http Request...");
        }

        $deployment = $this->deploymentService->findById($this->params('deployment'));

        $deployment->setStatus('RUNNING');
        $this->deploymentService->persist($deployment);

        $output = $this->deployer->deploy($deployment);

        $deployment->setStatus('COMPLETE');
        $this->deploymentService->persist($deployment);

        $hydrator = new DeploymentHydrator();

        return new JsonModel([
            'deployment' => $hydrator->extract($deployment),
            'textContent' => implode("\n", $output),
        ]);
    }
}