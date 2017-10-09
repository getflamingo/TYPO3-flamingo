<?php

namespace Ubermanu\Flamingo\Service;

use Flamingo\Flamingo;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Fluid\View\StandaloneView;
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
     * @var string
     */
    protected $typo3ConfigurationFilename = 'EXT:flamingo/Configuration/Yaml/TYPO3.yaml';

    /**
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @param ConfigurationManager $configurationManager
     * @internal
     */
    public function injectConfigurationManager(ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Include sources once and instantiate Flamingo task runner
     * Include default configuration + all the additional files
     */
    public function initializeObject()
    {
        ConfigurationUtility::requireLibraries();
        $this->flamingo = GeneralUtility::makeInstance(Flamingo::class);

        // Load default configuration files from flamingo.phar
        foreach (ConfigurationUtility::defaultConfigurationFiles() as $configurationFilename) {
            $this->flamingo->addConfiguration(file_get_contents($configurationFilename));
        }

        // Load additional configuration from TYPO3
        // This will be added at the top of the generated custom file
        $typo3configuration = $this->generateTypo3Configuration();

        // Load configuration files from the TS settings
        foreach ($this->getConfigurationFiles() as $configuration) {
            $this->flamingo->addConfiguration($typo3configuration . file_get_contents(GeneralUtility::getFileAbsFileName($configuration)));
        }
    }

    /**
     * Generate a YAML file using Fluid rendering engine
     * Pass TYPO3_CONF_VARS into it so global definitions can be used
     * See the actual TYPO3.yaml for more information
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
     * Return the configuration array
     *
     * @return array
     */
    protected function getConfigurationFiles()
    {
        $settings = $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $yamlConfigurations = (array)$settings['plugin.']['tx_flamingo.']['settings.']['yamlConfigurations.'];

        return GeneralUtility::removeDotsFromTS($yamlConfigurations);
    }

    /**
     * Parse given configuration
     */
    public function parseConfiguration()
    {
        $this->flamingo->parseConfiguration();
    }

    /**
     * @param string $taskName
     */
    public function run($taskName = 'default')
    {
        $this->flamingo->run($taskName);
    }
}
