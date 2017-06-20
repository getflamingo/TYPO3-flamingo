<?php

namespace Ubermanu\Flamingo\Utility;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\NoSuchOptionException;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class ExtensionUtility
 * @package Ubermanu\Flamingo\Utility
 */
class ExtensionUtility
{
    /**
     * @param string $option
     * @return mixed
     * @throws NoSuchOptionException
     */
    protected static function getExtensionConfiguration($option)
    {
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['flamingo']);

        if (false === array_key_exists($option, $extConf)) {
            throw new NoSuchOptionException(
                sprintf('The option "%s" does not exist, please check your LocalConfiguration.php!', $option)
            );
        }

        return $extConf[$option];
    }

    /**
     * Return the full path to the flamingo.phar executable
     * @return string
     * @throws FileDoesNotExistException
     */
    protected static function getPharPath()
    {
        $pharPath = GeneralUtility::getFileAbsFileName(self::getExtensionConfiguration('phar'));

        if (false === file_exists($pharPath)) {
            throw new FileDoesNotExistException(sprintf('The file "%s" does not exist!', $pharPath));
        }

        return $pharPath;
    }

    /**
     * Requires Flamingo source files so Flamingo can run in a TYPO3 env
     * @throws \Exception
     */
    public static function requireLibraries()
    {
        @include 'phar://' . self::getPharPath() . '/vendor/autoload.php';

        if (false === class_exists('Flamingo\\Flamingo')) {
            throw new \Exception('Flamingo resources could not be loaded from the *.phar file!');
        }
    }

    /**
     * Return the path to the default configuration file
     * @return string
     */
    public static function defaultConfigurationFileName()
    {
        return 'phar://' . self::getPharPath() . '/bin/default.yml';
    }

    /**
     * Get a list of configuration files
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
        return boolval(self::getExtensionConfiguration('debug'));
    }

    /**
     * @return bool
     */
    public static function isForceEnabled()
    {
        return boolval(self::getExtensionConfiguration('force'));
    }
}
