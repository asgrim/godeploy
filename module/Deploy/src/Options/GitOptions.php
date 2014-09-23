<?php

namespace Deploy\Options;

use Zend\Stdlib\AbstractOptions;
use Deploy\Shell\Shell;

class GitOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $cacheDirectory;

    protected $shell;

    /**
	 * @return string
	 */
    public function getCacheDirectory()
    {
        if (!file_exists($this->cacheDirectory)) {
            mkdir($this->cacheDirectory, 0755, true);
        }
        return $this->cacheDirectory;
    }

    /**
	 * @param string $privateKey
	 * @return \Deploy\Options\SshOptions
	 */
    public function setCacheDirectory($cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
    }

    public function getShell()
    {
        if (!isset($this->shell)) {
            $this->shell = new Shell();
        }

        return $this->shell;
    }
}
