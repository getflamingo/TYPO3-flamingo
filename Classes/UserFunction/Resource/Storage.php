<?php

namespace Ubermanu\Flamingo\UserFunction\Extbase;

use Flamingo\Core\TaskRuntime;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Storage
 * @package Ubermanu\Flamingo\UserFunction\Extbase
 */
class Storage
{
    /**
     * @var array
     */
    protected static $defaultConfiguration = [
        '__source' => 0,
        'storage' => 0,
    ];

    /**
     * Create a file.
     *
     * @param array $configuration
     * @param TaskRuntime $taskRuntime
     */
    public static function createFile(array $configuration, TaskRuntime $taskRuntime)
    {
        $configuration = array_replace(self::$defaultConfiguration, $configuration);
        $storage = self::getStorage($configuration['storage']);
        $folder = $storage->getFolder($configuration['folder']);

        if (false === $storage->hasFile($configuration['file']) || $configuration['overwrite']) {
            $storage->createFile($configuration['file'], $folder);
        }
    }

    /**
     * Create a folder.
     *
     * @param array $configuration
     * @param TaskRuntime $taskRuntime
     */
    public static function createFolder(array $configuration, TaskRuntime $taskRuntime)
    {
        $configuration = array_replace(self::$defaultConfiguration, $configuration);
        $storage = self::getStorage($configuration['storage']);

        if (false === $storage->hasFolder($configuration['folder']) || $configuration['overwrite']) {
            $storage->createFolder($configuration['folder']);
        }
    }

    /**
     * Delete a single file.
     *
     * @param array $configuration
     * @param TaskRuntime $taskRuntime
     */
    public static function deleteFile(array $configuration, TaskRuntime $taskRuntime)
    {
        $configuration = array_replace(self::$defaultConfiguration, $configuration);
        $storage = self::getStorage($configuration['storage']);

        if ($storage->hasFile($configuration['file'])) {
            $storage->deleteFile($configuration['file']);
        }
    }

    /**
     * Delete a folder.
     *
     * @param array $configuration
     * @param TaskRuntime $taskRuntime
     */
    public static function deleteFolder(array $configuration, TaskRuntime $taskRuntime)
    {
        $configuration = array_replace(self::$defaultConfiguration, $configuration);
        $storage = self::getStorage($configuration['storage']);

        if ($storage->hasFolder($configuration['folder'])) {
            $storage->deleteFolder($configuration['folder']);
        }
    }

    /**
     * Import file from URL
     * TODO: Add more error feedback
     * TODO: Return the file reference
     *
     * @param array $configuration
     * @param TaskRuntime $taskRuntime
     * @recursive
     */
    public static function importFile(array $configuration, TaskRuntime $taskRuntime)
    {
        // Replace default configuration
        $configuration = array_replace(self::$defaultConfiguration, $configuration);

        $storage = self::getStorage($configuration['storage']);
        $folder = $storage->getFolder($configuration['folder']);

        /** @var \Flamingo\Core\Table $source */
        $source = $taskRuntime->getTableByIdentifier($configuration['__source']);

        foreach ($source as $row ) {

            $filename = basename($row[$configuration['filename']]);
            $filename = $storage->sanitizeFileName($filename, $folder);

            $url = $configuration['url'];

            // Download image if it does not exist (or overwrite if defined)
            if (false === $storage->hasFile($filename) || $configuration['overwrite']) {
                if ($content = GeneralUtility::getUrl($url)) {
                    $file = $storage->createFile($filename, $folder);
                    $file->setContents($content);
                }
            }
        }
    }

    /**
     * Get storage from configuration.
     * If uid is empty, returns the default one.
     *
     * @param int $uid
     * @return null|\TYPO3\CMS\Core\Resource\ResourceStorage
     */
    protected static function getStorage($uid)
    {
        return empty($uid)
            ? ResourceFactory::getInstance()->getDefaultStorage()
            : ResourceFactory::getInstance()->getStorageObject($uid);
    }
}
