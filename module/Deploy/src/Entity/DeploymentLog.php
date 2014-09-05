<?php

namespace Deploy\Entity;

class DeploymentLog
{
    /**
     * @var int
     */
    protected $deploymentId;

    /**
     * @var string
     */
    protected $log;

    /**
	 * @return int
	 */
    public function getDeploymentId()
    {
        return $this->deploymentId;
    }

    /**
	 * @param int $id
	 * @return \Deploy\Entity\DeploymentLog
	 */
    public function setDeploymentId($deploymentId)
    {
        $this->deploymentId = (int) $deploymentId;

        return $this;
    }

    /**
	 * @return string
	 */
    public function getLog()
    {
        return $this->log;
    }

    /**
	 * @param string $log
	 * @return \Deploy\Entity\DeploymentLog
	 */
    public function setLog($log)
    {
        $this->log = $log;

        return $this;
    }
}
