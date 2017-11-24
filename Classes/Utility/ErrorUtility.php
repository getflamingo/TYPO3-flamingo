<?php

namespace Ubermanu\Flamingo\Utility;

/**
 * Class ErrorUtility
 * @package Ubermanu\Flamingo\Utility
 */
class ErrorUtility
{
    /**
     * @var array
     */
    protected static $codeName = [
        \Analog\Analog::DEBUG => 'DEBUG',
        \Analog\Analog::INFO => 'INFO',
        \Analog\Analog::NOTICE => 'INFO',
        \Analog\Analog::WARNING => 'WARN',
        \Analog\Analog::ERROR => 'ERROR',
        \Analog\Analog::CRITICAL => 'ERROR',
        \Analog\Analog::ALERT => 'ERROR',
        \Analog\Analog::URGENT => 'ERROR'
    ];

    /**
     * Retrieve error code from an error level
     *
     * @param $errorLevel
     * @return mixed|string
     */
    public static function getCode($errorLevel)
    {
        return array_key_exists($errorLevel, self::$codeName) ? self::$codeName[$errorLevel] : '';
    }

    /**
     * Generate a formatted message from the given error
     *
     * @param $error
     * @return bool|string
     */
    public static function getMessage($error)
    {
        return sprintf('[%s] %s', self::getCode($error['level']), $error['message']);
    }
}
