<?php

namespace Ubermanu\Flamingo\Service;

use Flamingo\Flamingo;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ubermanu\Flamingo\Exception\FileNotFoundException;
use Ubermanu\Flamingo\Utility\ConfigurationUtility;

/**
 * Class FlamingoService
 * @package Ubermanu\Flamingo\Service
 */
class FlamingoService implements SingletonInterface
{
    /**
     * @var Flamingo;
     */
    protected $flamingo = null;

    /**
     * Include sources once and instantiate Flamingo task runner.
     *
     * Since it's a singleton, configuration won't be loaded twice
     * TODO: Implement a reset() method to remove any remnant configuration
     *
     * @internal
     */
    public function initializeObject()
    {
        ConfigurationUtility::requireLibraries();
        $this->flamingo = GeneralUtility::makeInstance(Flamingo::class);

        // Load default configuration files from flamingo.phar
        foreach (ConfigurationUtility::defaultConfigurationFiles() as $configurationFilename) {
            $this->flamingo->addConfiguration(file_get_contents($configurationFilename));
        }
    }

    /**
     * Add a new configuration file into task runner.
     *
     * @param string $filename
     * @param bool $includeTypo3Configuration
     * @throws FileNotFoundException
     */
    public function addConfiguration($filename, $includeTypo3Configuration = true)
    {
        $filename = GeneralUtility::getFileAbsFileName($filename);

        if (false === file_exists($filename)) {
            throw new FileNotFoundException(
                sprintf('The configuration file "%s" could not be found.', $filename)
            );
        }

        $configuration = file_get_contents($filename);

        if ($includeTypo3Configuration) {
            $configuration = ConfigurationUtility::generateTypo3Configuration() . $configuration;
        }

        $this->flamingo->addConfiguration($configuration);
    }

    /**
     * Parse the global configuration.
     * This is mostly done once every configuration files have been added.
     */
    public function parseConfiguration()
    {
        $this->flamingo->parseConfiguration();
    }

    /**
     * Run a specific task.
     * The task is dependent of its context and current configuration.
     *
     * @param string $taskName
     */
    public function run($taskName = 'default')
    {
        $this->flamingo->run($taskName);
    }
}
