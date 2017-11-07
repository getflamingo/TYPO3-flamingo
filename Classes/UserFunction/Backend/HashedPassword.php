<?php

namespace Ubermanu\Flamingo\UserFunction\Backend;

use Flamingo\Core\TaskRuntime;
use TYPO3\CMS\Saltedpasswords\Salt\SaltFactory;

/**
 * Class HashedPassword
 * @package Ubermanu\Flamingo\UserFunction
 */
class HashedPassword
{
    /**
     * @var array
     */
    protected static $defaultConfiguration = [
        '__source' => 0,
        'mode' => 'FE',
        'readablePasswordColumn' => 'password_clear',
        'hashedPasswordColumn' => 'password',
    ];

    /**
     * Take the value from "readablePasswordColumn" and convert it into a hashed password
     * The hashed value is stored into "hashedPasswordColumn"
     * A random password is generated if given value is empty
     *
     * @param array $configuration
     * @param TaskRuntime $taskRuntime
     * @return mixed
     * @recursive
     */
    public static function run(array $configuration, TaskRuntime $taskRuntime)
    {
        // Replace default configuration
        $configuration = array_replace(self::$defaultConfiguration, $configuration);

        /** @var \Flamingo\Core\Table $source */
        $source = $taskRuntime->getTableByIdentifier($configuration['__source']);

        // Create salt factory instance
        $saltFactory = SaltFactory::getSaltingInstance(null, $configuration['mode']);

        // Update the existing properties
        foreach ($source as &$row) {
            $password = $row[$configuration['readablePasswordColumn']] ?: self::randomPassword();
            $row[$configuration['readablePasswordColumn']] = $password;
            $row[$configuration['hashedPasswordColumn']] = $saltFactory->getHashedPassword($password);
        }

        return 0;
    }

    /**
     * Generate a random password
     *
     * @return string
     */
    protected static function randomPassword()
    {
        return bin2hex(random_bytes(5));
    }
}
