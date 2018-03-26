<?php

namespace Ubermanu\Flamingo\Core\Reader;

/**
 * Class DatabaseReader
 * @package Ubermanu\Flamingo\Core\Reader
 */
class DatabaseReader extends \Flamingo\Reader\DatabaseReader
{
    /**
     * DatabaseReader constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options['server'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['host'];
        $this->options['port'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['port'];
        $this->options['username'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['user'];
        $this->options['password'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['password'];
        $this->options['database'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['dbname'];
        $this->options['charset'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['charset'];

        parent::__construct($options);
    }
}
