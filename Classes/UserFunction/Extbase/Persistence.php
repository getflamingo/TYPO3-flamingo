<?php

namespace Ubermanu\Flamingo\UserFunction\Extbase;

use Flamingo\Core\TaskRuntime;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Class Persistence
 * @package Ubermanu\Flamingo\UserFunction\Extbase
 */
class Persistence
{
    /**
     * @var array
     */
    protected static $defaultConfiguration = [
        '__source' => 0,
        'objectType' => '',
    ];

    /**
     * @param array $configuration
     * @param TaskRuntime $taskRuntime
     * @recursive
     */
    public static function add(array $configuration, TaskRuntime $taskRuntime)
    {
        // Replace default configuration
        $configuration = array_replace(self::$defaultConfiguration, $configuration);

        /** @var \Flamingo\Core\Table $source */
        $source = $taskRuntime->getTableByIdentifier($configuration['__source']);

        foreach ($source as $row) {
            // TODO: Transform row to objectType
            // TODO: Add object
        }
    }

    /**
     * @param array $configuration
     * @param TaskRuntime $taskRuntime
     * @recursive
     */
    public static function update(array $configuration, TaskRuntime $taskRuntime)
    {
        // Replace default configuration
        $configuration = array_replace(self::$defaultConfiguration, $configuration);

        /** @var \Flamingo\Core\Table $source */
        $source = $taskRuntime->getTableByIdentifier($configuration['__source']);

        foreach ($source as $row) {
            // TODO: Transform row to objectType
            // TODO: Update or add if it does not exist
        }
    }

    /**
     * @param array $configuration
     * @param TaskRuntime $taskRuntime
     * @recursive
     */
    public static function remove(array $configuration, TaskRuntime $taskRuntime)
    {
        // Replace default configuration
        $configuration = array_replace(self::$defaultConfiguration, $configuration);

        /** @var \Flamingo\Core\Table $source */
        $source = $taskRuntime->getTableByIdentifier($configuration['__source']);

        foreach ($source as $row) {
            // TODO: Transform row to objectType
            // TODO: Remove object
        }
    }

    /**
     * Clears the in-memory state of the persistence.
     *
     * @param array $configuration
     * @param TaskRuntime $taskRuntime
     */
    public static function clearState(array $configuration, TaskRuntime $taskRuntime)
    {

        self::getPersistenceManager()->clearState();
    }

    /**
     * Apply changes to persistence layer.
     *
     * @param array $configuration
     * @param TaskRuntime $taskRuntime
     */
    public static function persistAll(array $configuration, TaskRuntime $taskRuntime)
    {
        self::getPersistenceManager()->persistAll();
    }

    /**
     * @return PersistenceManager
     */
    protected static function getPersistenceManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class)->get(PersistenceManager::class);
    }
}
