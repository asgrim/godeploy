<?php

namespace Deploy\Entity;

class Task
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $projectId;

    /**
     * @var int
     */
    protected $order;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string[]
     */
    protected $onlyOnTargets = [];

    /**
     * @var string[]
     */
    protected $notOnTargets = [];

    /**
	 * @return int
	 */
    public function getId()
    {
        return $this->id;
    }

    /**
	 * @param int $id
     * @return \Deploy\Entity\Task
	 */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
	 * @return int
	 */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
	 * @param int $projectId
     * @return \Deploy\Entity\Task
	 */
    public function setProjectId($projectId)
    {
        $this->projectId = (int) $projectId;

        return $this;
    }

    /**
	 * @return int
	 */
    public function getOrder()
    {
        return $this->order;
    }

    /**
	 * @param int $order
     * @return \Deploy\Entity\Task
	 */
    public function setOrder($order)
    {
        $this->order = (int) $order;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
	 * @param string $command
     * @return \Deploy\Entity\Task
	 */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    public function getPreparedCommand(Deployment $deployment, \ZfcUser\Entity\User $user)
    {
        $command = $this->getCommand();

        $revision = $deployment->getResolvedRevision();
        $command = str_ireplace("#git-update#", "git fetch origin && git checkout $revision", $command);
        $command = str_ireplace("#revision#", $revision, $command);
        $command = str_ireplace("#user#", $user->getDisplayName(), $command);
        $command = str_ireplace("#comment#", $deployment->getComment(), $command);

        return $command;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
	 * @param string $directory
     * @return \Deploy\Entity\Task
	 */
    public function setDirectory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
	 * @return string
	 */
    public function getOnlyOnTargets($separator = ',')
    {
        return implode($separator, $this->onlyOnTargets);
    }

    /**
	 * @param string $onlyOnTargets
     * @return \Deploy\Entity\Task
	 */
    public function setOnlyOnTargets($onlyOnTargets)
    {
        if (strlen($onlyOnTargets) > 0) {
            $this->onlyOnTargets = explode(',', $onlyOnTargets);
        } else {
            $this->onlyOnTargets = [];
        }

        return $this;
    }

    /**
	 * @return string
	 */
    public function getNotOnTargets($separator = ',')
    {
        return implode($separator, $this->notOnTargets);
    }

    /**
	 * @param string $notOnTargets
     * @return \Deploy\Entity\Task
	 */
    public function setNotOnTargets($notOnTargets)
    {
        if (strlen($notOnTargets) > 0) {
            $this->notOnTargets = explode(',', $notOnTargets);
        } else {
            $this->notOnTargets = [];
        }

        return $this;
    }

    public function allowedOnTarget(Target $target)
    {
        if (count($this->notOnTargets) > 0 && in_array($target->getName(), $this->notOnTargets)) {
            return false;
        }

        if (count($this->onlyOnTargets) > 0 && !in_array($target->getName(), $this->onlyOnTargets)) {
            return false;
        }

        return true;
    }
}
