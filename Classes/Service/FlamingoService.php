<?php

namespace Ubermanu\Flamingo\Service;

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
     * @var \Flamingo\Flamingo
     */
    protected $flamingo = null;

    /**
     * Include sources once and instantiate Flamingo task runner.
     * Since it's a singleton, configuration won't be loaded twice.
     */
    public function __construct()
    {
        ConfigurationUtility::requireLibraries();
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

        foreach (ConfigurationUtility::defaultConfigurationFiles() as $configurationFilename) {
            $this->flamingo->addConfiguration(file_get_contents($configurationFilename));
        }

        return $this;
    }

    /**
     * Add a new configuration file into task runner.
     *
     * @param string $filename
     * @param bool $includeTypo3Configuration
     * @return $this
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

        return $this;
    }

    /**
     * Parse the global configuration.
     * This is mostly done once every configuration files have been added.
     *
     * @return $this
     */
    public function parseConfiguration()
    {
        $this->flamingo->parseConfiguration();

        return $this;
    }

    /**
     * Run a specific task
     * The task is dependent of its context and current configuration.
     *
     * @param string $taskName
     * @return $this
     */
    public function run($taskName = 'default')
    {
        $this->flamingo->run($taskName);

        return $this;
    }
}
