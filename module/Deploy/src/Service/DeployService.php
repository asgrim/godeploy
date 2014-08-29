<?php

namespace Deploy\Service;

use Deploy\Entity\Project;
use Deploy\Entity\Target;
use Deploy\Connection\SshConnection;
use Deploy\Options\SshOptions;

class DeployService
{
    /**
     * @var string[]
     */
    protected $output;

    /**
     * @var \Deploy\Options\SshOptions
     */
    protected $sshOptions;

    public function __construct(SshOptions $sshOptions)
    {
        $this->sshOptions = $sshOptions;
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

    public function deploy(Project $project)
    {
        $this->output = [];

        $this->output("Commence deployment: " . date("Y-m-d H:i:s"));
        $this->outputNewline(2);

        $targets = $project->getTargets();

        foreach ($targets as $target) {
            $header = "Deploy to target: " . $target->getName();
            $this->output($header);
            $this->output(str_repeat("=", strlen($header)));

            $this->deployToTarget($project, $target);

            $this->outputNewline(2);
        }

        $this->output("Finish deployment: " . date("Y-m-d H:i:s"));

        return $this->output;
    }

    public function deployToTarget(Project $project, Target $target)
    {
        $ssh = new SshConnection($target, $this->sshOptions);
        $ssh->connect();

        $tasks = $project->getTasks();
        foreach ($tasks as $task) {
            if (!$task->allowedOnTarget($target)) continue;

            $dir = is_null($task->getDirectory()) ? $target->getDirectory() : $task->getDirectory();

            $this->outputNewline();
            $this->output($dir . "$ {$task->getCommand()}");

            $result = $ssh->execute($task->getCommand(), $dir);

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
