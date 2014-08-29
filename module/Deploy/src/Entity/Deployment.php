<?php

namespace Deploy\Entity;

class Deployment
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var int
     */
    protected $projectId;

    /**
     * @var \DateTime
     */
    protected $dateAdded;

    /**
     * @var string
     */
    protected $revision;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var string
     */
    protected $status;

    public function __construct()
    {
        $this->dateAdded = new \DateTime();
    }

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return \Deploy\Entity\Deployment
	 */
	public function setId($id)
	{
		$this->id = (int)$id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @param int $userId
	 * @return \Deploy\Entity\Deployment
	 */
	public function setUserId($userId)
	{
		$this->userId = (int)$userId;
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
	 * @return \Deploy\Entity\Deployment
	 */
	public function setProjectId($projectId)
	{
		$this->projectId = (int)$projectId;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDateAdded($format = 'Y-m-d H:i:s')
	{
		return $this->dateAdded->format($format);
	}

	/**
	 * @param string|\DateTime $dateAdded
	 * @return \Deploy\Entity\Deployment
	 */
	public function setDateAdded($dateAdded)
	{
	    if ($dateAdded instanceof \DateTime) {
	        $this->dateAdded = $dateAdded;
	    } else {
	        $this->dateAdded = new \DateTime($dateAdded);
	    }
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRevision()
	{
		return $this->revision;
	}

	/**
	 * @param string $revision
	 * @return \Deploy\Entity\Deployment
	 */
	public function setRevision($revision)
	{
		$this->revision = $revision;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getComment()
	{
		return $this->comment;
	}

	/**
	 * @param string $comment
	 * @return \Deploy\Entity\Deployment
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param string $status
	 * @return \Deploy\Entity\Deployment
	 */
	public function setStatus($status)
	{
	    $validStatuses = ['PREVIEW', 'RUNNING', 'COMPLETE', 'FAILED'];
	    if (!in_array($status, $validStatuses)) {
	        throw new \InvalidArgumentException(sprintf('Status "%s" was not a valid status type'));
	    }

		$this->status = $status;
		return $this;
	}

}