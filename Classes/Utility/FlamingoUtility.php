<?php

namespace Ubermanu\Flamingo\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Ubermanu\Flamingo\Exception\FileNotFoundException;

/**
 * Class FlamingoUtility
 * @package Ubermanu\Flamingo\Utility
 */
class FlamingoUtility
{
    /**
     * @var string
     */
    protected static $typo3ConfigurationFileName = 'EXT:flamingo/Configuration/Flamingo/TYPO3.yml';

    /**
     * Generate a YML file using Fluid rendering engine
     * Pass TYPO3_CONF_VARS into it so global definitions can be used
     * See the actual TYPO3.yml for more information
     * TODO: Put this in cache for performance purposes
     */
    protected static function generateTypo3Configuration()
    {
        $typo3ConfigurationFileName = GeneralUtility::getFileAbsFileName(self::$typo3ConfigurationFileName);

        // File does not exist, throw error
        if (false === file_exists($typo3ConfigurationFileName)) {
            throw new FileNotFoundException(sprintf('The TYPO3 configuration file "%s" could not be found'));
        }

        /** @var StandaloneView $typo3Configuration */
        $typo3Configuration = GeneralUtility::makeInstance(StandaloneView::class);
        $typo3Configuration->setTemplatePathAndFilename($typo3ConfigurationFileName);
        $typo3Configuration->assign('TYPO3_CONF_VARS', $GLOBALS['TYPO3_CONF_VARS']);

        return $typo3Configuration->render();
    }

    /**
     * Return the configuration array
     *
     * @return array
     */
    public static function getConfigurationArray()
    {
        return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['flamingo']['configuration'] ?: [];
    }

    /**
     * @param string $fileName
     * @param bool $useTypo3Configuration
     * @throws FileNotFoundException
     */
    public static function registerConfigurationFile($fileName, $useTypo3Configuration = true)
    {
        // Find the absolute path to the configuration file (Supports EXT: syntax)
        $fileName = GeneralUtility::getFileAbsFileName($fileName);

        // File does not exist, throw error
        if (false === file_exists($fileName)) {
            throw new FileNotFoundException(sprintf('The configuration file "%s" could not be found'));
        }

        // Set configuration as array if not already defined
        if (false === is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['flamingo']['configuration'])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['flamingo']['configuration'] = [];
        }

        $configurationContent = file_get_contents($fileName);

        // Insert generated TYPO3 env at the top of the file
        if (true === $useTypo3Configuration) {
            $configurationContent = self::generateTypo3Configuration() . $configurationContent;
        }

        // Include into global array
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['flamingo']['configuration'][] = $configurationContent;
    }
}
