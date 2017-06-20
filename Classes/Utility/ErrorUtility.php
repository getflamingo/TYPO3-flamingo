<?php

namespace Ubermanu\Flamingo\Utility;

use Analog\Analog;

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
        Analog::DEBUG => 'DEBUG',
        Analog::INFO => 'INFO',
        Analog::NOTICE => 'INFO',
        Analog::WARNING => 'WARN',
        Analog::ERROR => 'ERROR',
        Analog::CRITICAL => 'ERROR',
        Analog::ALERT => 'ERROR',
        Analog::URGENT => 'ERROR'
    ];

    /**
     * @param $errorLevel
     * @return mixed|string
     */
    public static function getCode($errorLevel)
    {
        return array_key_exists($errorLevel, self::$codeName) ? self::$codeName[$errorLevel] : '';
    }

    /**
     * @param $error
     * @return bool|string
     */
    public static function getMessage($error)
    {
        return sprintf('[%s] %s', self::getCode($error['level']), $error['message']);
    }
}
