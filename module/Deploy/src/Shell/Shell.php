<?php

namespace Deploy\Shell;

class Shell
{
    private $lastOutput;

    private $lastErrorNumber;

    public function execute($command)
    {
        $this->lastErrorNumber = 0;
        $this->lastOutput = [];

        exec($command . " 2>&1", $this->lastOutput, $this->lastErrorNumber);

        return $this->lastOutput;
    }

    public function getLastErrorNumber()
    {
        return $this->lastErrorNumber;
    }

    public function getLastOutput()
    {
        return $this->lastOutput;
    }
}