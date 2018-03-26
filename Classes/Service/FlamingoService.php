<?php

namespace Ubermanu\Flamingo\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FlamingoService
 * @package Ubermanu\Flamingo\Service
 */
class FlamingoService implements SingletonInterface
{
    /**
     * @var \Flamingo\Flamingo
     */
    protected $flamingo = null;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * Include sources once and instantiate Flamingo task runner.
     * Since it's a singleton, configuration won't be loaded twice.
     */
    public function __construct()
    {
        $this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['flamingo']) ?: [];

        if ($bin = $this->configuration['bin']) {
            if (pathinfo($bin, PATHINFO_EXTENSION)) {
                $bin = 'phar://' . ltrim('/', $bin);
            }
            require_once $bin;
        }

        $this->reset();
    }

    /**
     * @return FlamingoService|object
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }

    /**
     * Reset current flamingo object
     * Load default configuration files from flamingo.phar
     *
     * @return $this
     */
    public function reset()
    {
        $this->flamingo = new \Flamingo\Flamingo;

        return $this;
    }

    /**
     * Run a specific task.
     * The task is dependent of its context and current configuration.
     *
     * @param string $taskName
     * @param mixed $arguments
     * @return $this
     */
    public function run($taskName, ...$arguments)
    {
        $this->flamingo->run($taskName, ...$arguments);

        return $this;
    }
}
