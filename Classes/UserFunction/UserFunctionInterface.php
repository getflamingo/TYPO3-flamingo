<?php

namespace Ubermanu\Flamingo\UserFunction;

use Flamingo\Core\TaskRuntime;

/**
 * Interface UserFunctionInterface
 * @package Ubermanu\Flamingo\UserFunction
 */
interface UserFunctionInterface
{
    /**
     * @param array $configuration
     * @param TaskRuntime $taskRuntime
     * @return mixed
     */
    public static function run(array $configuration, TaskRuntime $taskRuntime);
}
