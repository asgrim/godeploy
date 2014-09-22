<?php

namespace Deploy\Deployer;

use Deploy\Entity\Deployment;
use Deploy\Entity\Project;
use Deploy\Entity\Target;
use Deploy\Connection\SshConnection;
use Deploy\Options\SshOptions;
use Deploy\Service\DeploymentService;
use Deploy\Service\ProjectService;
use Deploy\Service\TargetService;
use Deploy\Service\TaskService;
use Deploy\Service\UserService;
use Deploy\Service\AdditionalFileService;
use ZfcUser\Entity\User;

class Deployer
{
    /**
     * @var string[]
     */
    protected $output;

    /**
     * @var boolean
     */
    protected $resolvedRevision;

    /**
     * @var \Deploy\Options\SshOptions
     */
    protected $sshOptions;

    /**
     * @var \Deploy\Service\DeploymentService
     */
    protected $deploymentService;

    /**
     * @var \Deploy\Service\ProjectService
     */
    protected $projectService;

    /**
     * @var \Deploy\Service\TargetService
     */
    protected $targetService;

    /**
     * @var \Deploy\Service\TaskService
     */
    protected $taskService;

    /**
     * @var \Deploy\Service\UserService
     */
    protected $userService;

    /**
     * @var \Deploy\Service\AdditionalFileService
     */
    protected $additionalFileService;

    public function __construct(
        SshOptions $sshOptions,
        DeploymentService $deploymentService,
        ProjectService $projectService,
        TargetService $targetService,
        TaskService $taskService,
        UserService $userService,
        AdditionalFileService $additionalFileService
    ) {
        $this->sshOptions = $sshOptions;
        $this->deploymentService = $deploymentService;
        $this->projectService = $projectService;
        $this->targetService = $targetService;
        $this->taskService = $taskService;
        $this->userService = $userService;
        $this->additionalFileService = $additionalFileService;
    }

    protected function outputNewline($count = 1)
    {
        for ($i = 0; $i < $count; $i++) {
            $this->output('');
        }
    }

    protected function output($line)
    {
        $this->output[] = $line;
    }

    public function getLastOutput()
    {
        return $this->output;
    }

    public function deploy(Deployment $deployment)
    {
        $this->output = [];

        if ($deployment->getStatus() != 'RUNNING') {
            throw new \RuntimeException('Deployment not at valid status...');
        }

        $project = $this->projectService->findById($deployment->getProjectId());
        $user = $this->userService->findById($deployment->getUserId());

        $this->output("Commence deployment: " . date("Y-m-d H:i:s"));
        $this->outputNewline(2);

        $targets = $this->targetService->findByProjectId($project->getId());

        foreach ($targets as $target) {
            $header = "Deploy to target: " . $target->getName();
            $this->output($header);
            $this->output(str_repeat("=", strlen($header)));

            $this->deployToTarget($project, $deployment, $target, $user);

            $this->outputNewline(2);
        }

        $this->output("Finish deployment: " . date("Y-m-d H:i:s"));

        return $this->output;
    }

    public function resolveRevision(Deployment $deployment, SshConnection $ssh, $directory)
    {
        if (!$deployment->hasResolvedRevision()) {
            $command = "git fetch origin && git show --format=format:%H --no-notes -s " . $deployment->getRevision();
            $result = $ssh->execute($command, $directory);
            if (!count($result['stdout'])) {
                foreach ($result['stderr'] as $line) {
                    $this->output($line);
                }
                throw new \RuntimeException(sprintf('Failed to resolve revision "%s"', $deployment->getRevision()));
            }

            $deployment->setResolvedRevision($result['stdout'][0]);
            $this->deploymentService->persist($deployment);
        }
    }

    public function deployToTarget(Project $project, Deployment $deployment, Target $target, User $user)
    {
        $ssh = new SshConnection($target, $this->sshOptions);
        $ssh->connect();

        $tasks = $this->taskService->findByProjectId($project->getId());

        foreach ($tasks as $task) {
            /* @var $task \Deploy\Entity\Task */
            if (!$task->allowedOnTarget($target)) {
                continue;
            }

            $dir = is_null($task->getDirectory()) ? $target->getDirectory() : $task->getDirectory();

            $this->resolveRevision($deployment, $ssh, $dir);

            $command = $task->getPreparedCommand($deployment, $user);

            $this->outputNewline();
            $this->output($dir . "$ {$command}");

            $result = $ssh->execute($command, $dir);

            foreach ($result['stderr'] as $line) {
                $this->output($line);
            }

            foreach ($result['stdout'] as $line) {
                $this->output($line);
            }
        }

        $additionalFiles = $this->additionalFileService->findByProjectId($project->getId());

        if (count($additionalFiles) > 0) {
            $this->outputNewline();
            $this->output('Deploying additional files...');

            foreach ($additionalFiles as $additionalFile) {
                /* @var $additionalFile \Deploy\Entity\AdditionalFile */
                if (!$additionalFile->allowedOnTarget($target)) {
                    continue;
                }

                $fullpath = $target->getDirectory() . '/' . $additionalFile->getFilename();

                $this->output($fullpath);
                $ssh->putFile($fullpath, $additionalFile->getContent());
            }
        }

        $ssh->disconnect();
    }
}
