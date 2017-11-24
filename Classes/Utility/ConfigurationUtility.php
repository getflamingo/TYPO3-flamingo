<?php

namespace Ubermanu\Flamingo\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Ubermanu\Flamingo\Exception\FileNotFoundException;
use Ubermanu\Flamingo\Exception\IncludedResourceException;

/**
 * Class ConfigurationUtility
 * @package Ubermanu\Flamingo\Utility
 */
class ConfigurationUtility
{
    /**
     * Get extension configuration from LocalConfiguration.php
     *
     * @return array
     */
    protected static function getExtensionConfiguration()
    {
        return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['flamingo']) ?: [];
    }

    /**
     * Get extension BE settings
     *
     * @return array
     */
    protected static function getExtensionSettings()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var ConfigurationManagerInterface $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);

        $settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'Flamingo'
        );

        return $settings;
    }

    /**
     * Return the full path to the flamingo.phar executable
     * Throws error if the file could not be found
     *
     * @return string
     * @throws FileNotFoundException
     */
    protected static function getPharPath()
    {
        $pharPath = self::translatePath(self::getExtensionConfiguration()['phar']);

        if (false === file_exists($pharPath)) {
            throw new FileNotFoundException(
                sprintf('The PHAR resource file "%s" does not exist!', $pharPath)
            );
        }

        return 'phar://' . $pharPath;
    }

    /**
     * Requires Flamingo source files so Flamingo can run in a TYPO3 env
     * Test if the main Flamingo class can be called (so we avoid future errors)
     *
     * @throws IncludedResourceException
     */
    public static function requireLibraries()
    {
        @include self::getPharPath() . '/vendor/autoload.php';

        if (false === class_exists('Flamingo\\Flamingo')) {
            throw new IncludedResourceException('Flamingo resources could not be loaded from the PHAR resource file!');
        }
    }

    /**
     * Return the path to the default configuration files
     * These files are contained in the *.phar resource
     *
     * @return array
     */
    public static function defaultConfigurationFiles()
    {
        return [
            self::getPharPath() . '/bin/DefaultConfiguration.yaml',
            self::getPharPath() . '/bin/AdditionalConfiguration.yaml',
        ];
    }

    /**
     * Get all the YAML files from the extension settings
     * and generate the additional TYPO3 configuration
     * TODO: Don't parse the real YAML files, move current to *.HTML
     *
     * @return string
     * @throws FileNotFoundException
     */
    public static function generateTypo3Configuration()
    {
        $yamlConfigurations = self::getExtensionSettings()['yamlConfigurations'];
        $generatedConfiguration = '';

        foreach ($yamlConfigurations as $filename) {
            $generatedConfiguration .= self::generateTypo3ConfigurationFromTemplate($filename);
        }

        return $generatedConfiguration;
    }

    /**
     * Generate a YAML configuration using Fluid rendering engine
     * Pass TYPO3_CONF_VARS into it so global definitions can be used
     *
     * @param string $template
     * @return string
     * @throws FileNotFoundException
     */
    protected static function generateTypo3ConfigurationFromTemplate($template)
    {
        $typo3ConfigurationFilename = GeneralUtility::getFileAbsFileName($template);

        // File does not exist, throw error
        if (false === file_exists($typo3ConfigurationFilename)) {
            throw new FileNotFoundException(
                sprintf('The TYPO3 configuration file "%s" could not be found!', $typo3ConfigurationFilename)
            );
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
     * Translate path relative to the website root.
     *
     * @param string $path
     * @return string
     */
    protected static function translatePath($path)
    {
        return (0 === strpos($path, '/') ? $path : GeneralUtility::getFileAbsFileName($path));
    }
}
