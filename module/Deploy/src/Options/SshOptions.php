<?php

namespace Deploy\Options;

use Zend\Stdlib\AbstractOptions;

class SshOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $publicKey;

    /**
     * @var string
     */
    protected $privateKey;

	/**
	 * @return string
	 */
	public function getPublicKey()
	{
		return $this->publicKey;
	}

	/**
	 * @param string $publicKey
	 * @return \Deploy\Options\SshOptions
	 */
	public function setPublicKey($publicKey)
	{
		$this->publicKey = $publicKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrivateKey()
	{
		return $this->privateKey;
	}

	/**
	 * @param string $privateKey
	 * @return \Deploy\Options\SshOptions
	 */
	public function setPrivateKey($privateKey)
	{
		$this->privateKey = $privateKey;
	}

}