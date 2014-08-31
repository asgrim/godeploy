<?php

namespace Deploy\Deployer;

use Deploy\Entity\Deployment;
use Deploy\Entity\Project;
use Deploy\Entity\Target;
use Deploy\Connection\SshConnection;
use Deploy\Options\SshOptions;
use Deploy\Service\ProjectService;
use Deploy\Service\TargetService;
use Deploy\Service\TaskService;

class Deployer
{
    /**
     * @var string[]
     */
    protected $output;

    /**
     * @var \Deploy\Options\SshOptions
     */
    protected $sshOptions;

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

    public function __construct(SshOptions $sshOptions, ProjectService $projectService, TargetService $targetService, TaskService $taskService)
    {
        $this->sshOptions = $sshOptions;
        $this->projectService = $projectService;
        $this->targetService = $targetService;
        $this->taskService = $taskService;
    }

    protected function outputNewline($count = 1)
    {
        for ($i = 0; $i < $count; $i++)
        {
            $this->output('');
        }
    }

    protected function output($line)
    {
        $this->output[] = $line;
    }

    public function deploy(Deployment $deployment)
    {
        if ($deployment->getStatus() != 'RUNNING')
        {
            throw new \RuntimeException('Deployment not at valid status...');
        }

        $project = $this->projectService->findById($deployment->getId());

        $this->output = [];

        $this->output("Commence deployment: " . date("Y-m-d H:i:s"));
        $this->outputNewline(2);

        $targets = $this->targetService->findByProjectId($project->getId());

        foreach ($targets as $target) {
            $header = "Deploy to target: " . $target->getName();
            $this->output($header);
            $this->output(str_repeat("=", strlen($header)));

            $this->deployToTarget($project, $deployment, $target);

            $this->outputNewline(2);
        }

        $this->output("Finish deployment: " . date("Y-m-d H:i:s"));

        return $this->output;
    }

    public function deployToTarget(Project $project, Deployment $deployment, Target $target)
    {
        $ssh = new SshConnection($target, $this->sshOptions);
        $ssh->connect();

        $tasks = $this->taskService->findByProjectId($project->getId());

        foreach ($tasks as $task) {
            /* @var $task \Deploy\Entity\Task */
            if (!$task->allowedOnTarget($target)) continue;

            $dir = is_null($task->getDirectory()) ? $target->getDirectory() : $task->getDirectory();

            $command = $task->getPreparedCommand($deployment);

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

        $ssh->disconnect();
    }
}
