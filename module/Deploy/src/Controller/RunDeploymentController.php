<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Deploy\Deployer\Deployer;
use Deploy\Service\DeploymentService;
use Deploy\Service\DeploymentLogService;
use Deploy\Mapper\DeploymentHydrator;
use Deploy\Entity\DeploymentLog;

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

    /**
     * @var \Deploy\Service\DeploymentLogService
     */
    protected $deploymentLogService;

    public function __construct(
        Deployer $deployer,
        DeploymentService $deploymentService,
        DeploymentLogService $deploymentLogService
    ) {
        $this->deployer = $deployer;
        $this->deploymentService = $deploymentService;
        $this->deploymentLogService = $deploymentLogService;
    }

    public function indexAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new \Exception("Must be an XML Http Request...");
        }

        $deployment = $this->deploymentService->findById($this->params('deployment'));

        $deployment->setStatus('RUNNING');
        $this->deploymentService->persist($deployment);

        try {
            $output = $this->deployer->deploy($deployment);

            $deployment->setStatus('COMPLETE');
        } catch (\Exception $exception) {
            $output = $this->deployer->getLastOutput();

            $output[] = '';
            $output[] = get_class($exception) . ': ' . $exception->getMessage();

            $deployment->setStatus('FAILED');
        }

        $this->deploymentService->persist($deployment);

        $deploymentLog = new DeploymentLog();
        $deploymentLog->setDeploymentId($deployment->getId());
        $deploymentLog->setLog(implode("\n", $output));
        $this->deploymentLogService->persist($deploymentLog);

        $hydrator = new DeploymentHydrator();

        return new JsonModel([
            'deployment' => $hydrator->extract($deployment),
            'textContent' => implode("\n", $output),
        ]);
    }
}
