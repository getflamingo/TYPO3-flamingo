<?php

namespace Ubermanu\Flamingo\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
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
     * Return the full path to the flamingo.phar executable
     * Throws error if the file could not be found
     *
     * @return string
     * @throws FileNotFoundException
     */
    protected static function getPharPath()
    {
        $pharPath = GeneralUtility::getFileAbsFileName(self::getExtensionConfiguration()['phar']);

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
     * Generate a YAML configuration using Fluid rendering engine
     * Pass TYPO3_CONF_VARS into it so global definitions can be used
     * See the actual TYPO3.yaml for more information
     *
     * @return string
     * @throws FileNotFoundException
     */
    public static function generateTypo3Configuration()
    {
        $typo3ConfigurationFilename = GeneralUtility::getFileAbsFileName(self::getExtensionConfiguration()['typo3configuration']);

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
}
