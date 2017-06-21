<?php

namespace Ubermanu\Flamingo\Utility;

use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
     * @throws FileDoesNotExistException
     */
    protected static function getPharPath()
    {
        $pharPath = GeneralUtility::getFileAbsFileName(self::getExtensionConfiguration()['phar']);

        if (false === file_exists($pharPath)) {
            throw new FileDoesNotExistException(sprintf('The file "%s" does not exist!', $pharPath));
        }

        return $pharPath;
    }

    /**
     * Requires Flamingo source files so Flamingo can run in a TYPO3 env
     * Test if the main Flamingo class can be called (so we avoid future errors)
     *
     * @throws IncludedResourceException
     */
    public static function requireLibraries()
    {
        @include 'phar://' . self::getPharPath() . '/vendor/autoload.php';

        if (false === class_exists('Flamingo\\Flamingo')) {
            throw new IncludedResourceException('Flamingo resources could not be loaded from the *.phar file!');
        }
    }

    /**
     * Return the path to the default configuration file
     * This file is contained in the *.phar resource
     *
     * @return string
     */
    public static function defaultConfigurationFileName()
    {
        return 'phar://' . self::getPharPath() . '/bin/default.yml';
    }

    /**
     * Check if debug mode is active
     *
     * @return bool
     */
    public static function isDebugEnabled()
    {
        return self::getExtensionConfiguration()['debug'] === '1';
    }

    /**
     * Check if force mode is active
     *
     * @return bool
     */
    public static function isForceEnabled()
    {
        return self::getExtensionConfiguration()['force'] === '1';
    }
}
