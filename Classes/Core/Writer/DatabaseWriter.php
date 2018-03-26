<?php

namespace Ubermanu\Flamingo\Core\Writer;

use Flamingo\Table;

/**
 * Class DatabaseWriter
 * @package Ubermanu\Flamingo\Core\Writer
 */
class DatabaseWriter extends \Flamingo\Writer\DatabaseWriter
{
    /**
     * DatabaseWriter constructor.
     * @param Table $table
     * @param array $options
     */
    public function __construct(Table $table, array $options)
    {
        $this->options['server'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['host'];
        $this->options['port'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['port'];
        $this->options['username'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['user'];
        $this->options['password'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['password'];
        $this->options['database'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['dbname'];
        $this->options['charset'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['charset'];

        parent::__construct($table, $options);
    }
}
