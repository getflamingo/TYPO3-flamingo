<?php

namespace Ubermanu\Flamingo\Utility;

use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ubermanu\Flamingo\Exception\IncludedResourceException;
use Ubermanu\Flamingo\Exception\NoSuchOptionException;

/**
 * Class ExtensionUtility
 * @package Ubermanu\Flamingo\Utility
 */
class ExtensionUtility
{
    /**
     * Find a certain option in the extConf array
     *
     * @param string $option
     * @param bool $graceful
     * @return mixed
     * @throws NoSuchOptionException
     */
    protected static function getConfigurationOption($option, $graceful = false)
    {
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['flamingo']);

        if ((false === array_key_exists($option, $extConf)) && ($graceful === false)) {
            throw new NoSuchOptionException(
                sprintf('The option "%s" does not exist in the "flamingo" extension configuration', $option)
            );
        }

        return $extConf[$option];
    }

    /**
     * Return the full path to the flamingo.phar executable
     *
     * @return string
     * @throws FileDoesNotExistException
     */
    protected static function getPharPath()
    {
        $pharPath = GeneralUtility::getFileAbsFileName(self::getConfigurationOption('phar'));

        if (false === file_exists($pharPath)) {
            throw new FileDoesNotExistException(sprintf('The file "%s" does not exist!', $pharPath));
        }

        return $pharPath;
    }

    /**
     * Requires Flamingo source files so Flamingo can run in a TYPO3 env
     *
     * @throws \Exception
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
     *
     * @return string
     */
    public static function defaultConfigurationFileName()
    {
        return 'phar://' . self::getPharPath() . '/bin/default.yml';
    }

    /**
     * Get a list of configuration files
     *
     * @return array
     */
    public static function getConfigurationFiles()
    {
        return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['flamingo']['configuration'] ?: [];
    }

    /**
     * @return bool
     */
    public static function isDebugEnabled()
    {
        return self::getConfigurationOption('debug') === '1';
    }

    /**
     * @return bool
     */
    public static function isForceEnabled()
    {
        return self::getConfigurationOption('force') === '1';
    }
}
