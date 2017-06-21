<?php

namespace Ubermanu\Flamingo\Service;

use Flamingo\Flamingo;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ubermanu\Flamingo\Utility\ConfigurationUtility;
use Ubermanu\Flamingo\Utility\FlamingoUtility;

/**
 * Class FlamingoService
 * @package Ubermanu\Flamingo\Service
 */
class FlamingoService implements SingletonInterface
{
    /**
     * @var Flamingo;
     */
    protected $flamingo;

    /**
     * Include sources once and instantiate Flamingo task runner
     * Include default configuration + all the additional files
     */
    public function initializeObject()
    {
        ConfigurationUtility::requireLibraries();
        $this->flamingo = GeneralUtility::makeInstance(Flamingo::class);

        // Load default configuration from flamingo.phar
        $defaultConfiguration = ConfigurationUtility::defaultConfigurationFileName();
        $this->flamingo->addConfiguration(file_get_contents($defaultConfiguration));

        // Load configuration content from the GLOBALS array
        foreach (FlamingoUtility::getConfigurationArray() as $configuration) {
            $this->flamingo->addConfiguration($configuration);
        }
    }

    /**
     * @param string $taskName
     */
    public function run($taskName = 'default')
    {
        $this->flamingo->run($taskName);
    }
}
