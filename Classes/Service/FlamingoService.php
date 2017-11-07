<?php

namespace Ubermanu\Flamingo\Service;

use Flamingo\Flamingo;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Ubermanu\Flamingo\Exception\FileNotFoundException;
use Ubermanu\Flamingo\Utility\ConfigurationUtility;

/**
 * Class FlamingoService
 * @package Ubermanu\Flamingo\Service
 */
class FlamingoService
{
    /**
     * @var Flamingo;
     */
    protected $flamingo = null;

    /**
     * @var string
     */
    protected $extensionName = 'Flamingo';

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var string
     */
    protected $typo3ConfigurationFilename = 'EXT:flamingo/Configuration/Yaml/TYPO3.yaml';

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @param ConfigurationManagerInterface $configurationManager
     * @internal
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Include sources once and instantiate Flamingo task runner
     * Load default configuration files and extension settings
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

        // Load extension BE settings
        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            $this->extensionName
        );
    }

    /**
     * Generate a YAML file using Fluid rendering engine
     * Pass TYPO3_CONF_VARS into it so global definitions can be used
     * See the actual TYPO3.yaml for more information
     * TODO: Move into ConfigurationUtility
     */
    protected function generateTypo3Configuration()
    {
        $typo3ConfigurationFilename = GeneralUtility::getFileAbsFileName($this->typo3ConfigurationFilename);

        // File does not exist, throw error
        if (false === file_exists($typo3ConfigurationFilename)) {
            throw new FileNotFoundException(sprintf('The TYPO3 configuration file "%s" could not be found',
                $typo3ConfigurationFilename));
        }

        $TYPO3_CONF_VARS = $GLOBALS['TYPO3_CONF_VARS'];

        // Add compatibility for previous TYPO3 versions
        if (VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 8000000) {
            $TYPO3_CONF_VARS['DB']['Connections']['Default'] = $TYPO3_CONF_VARS['DB'];
            $TYPO3_CONF_VARS['DB']['Connections']['Default']['user'] = $TYPO3_CONF_VARS['DB']['username'];
            $TYPO3_CONF_VARS['DB']['Connections']['Default']['dbname'] = $TYPO3_CONF_VARS['DB']['database'];
        }

        /** @var StandaloneView $typo3Configuration */
        $typo3Configuration = GeneralUtility::makeInstance(StandaloneView::class);
        $typo3Configuration->setTemplatePathAndFilename($typo3ConfigurationFilename);
        $typo3Configuration->assign('TYPO3_CONF_VARS', $GLOBALS['TYPO3_CONF_VARS']);

        return $typo3Configuration->render();
    }

    /**
     * Add a new configuration file into task runner
     * TODO: Throw error if configuration file does not exist
     *
     * @param string $filename
     * @param bool $includeTypo3Configuration
     */
    public function addConfiguration($filename, $includeTypo3Configuration = true)
    {
        $configuration = file_get_contents(GeneralUtility::getFileAbsFileName($filename));

        if ($includeTypo3Configuration) {
            $configuration = $this->generateTypo3Configuration() . $configuration;
        }

        $this->flamingo->addConfiguration($configuration);
    }

    /**
     * Parse the global configuration
     * This is mostly done once every configuration files have been added
     */
    public function parseConfiguration()
    {
        $this->flamingo->parseConfiguration();
    }

    /**
     * Run a specific task
     * The task is dependent of its context and current configuration
     *
     * @param string $taskName
     */
    public function run($taskName = 'default')
    {
        $this->flamingo->run($taskName);
    }
}
