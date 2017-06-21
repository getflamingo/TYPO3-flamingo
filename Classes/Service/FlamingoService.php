<?php

namespace Ubermanu\Flamingo\Service;

use Flamingo\Flamingo;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ubermanu\Flamingo\Exception\FileNotFoundException;
use Ubermanu\Flamingo\Utility\ExtensionUtility;

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
        ExtensionUtility::requireLibraries();
        $this->flamingo = GeneralUtility::makeInstance(Flamingo::class);

        // Load default configuration from flamingo.phar
        $this->flamingo->addConfiguration(ExtensionUtility::defaultConfigurationFileName());

        // Load configuration from the GLOBALS array
        foreach (ExtensionUtility::getConfigurationFiles() as $fileName) {

            // Find the absolute path to the configuration file (Supports EXT: syntax)
            $fileName = GeneralUtility::getFileAbsFileName($fileName);

            if (false === file_exists($fileName)) {
                throw new FileNotFoundException(sprintf('The configuration file "%s" could not be found'));
            }

            $this->flamingo->addConfiguration(file_get_contents($fileName));
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
