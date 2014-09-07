<?php

namespace Deploy\Entity;

class AdditionalFile
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
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $content;

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
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param  string $filename
     * @return \Deploy\Entity\Task
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
	 * @param string $content
     * @return \Deploy\Entity\Task
	 */
    public function setContent($content)
    {
        $this->content = $content;
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
