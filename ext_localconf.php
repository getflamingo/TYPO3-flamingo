<?php
defined('TYPO3_MODE') or die;

if (TYPO3_MODE === 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][$_EXTKEY] =
        \Ubermanu\Flamingo\Command\FlamingoCommandController::class;
}

if (false === is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['flamingo']['configuration'])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['flamingo']['configuration'] = [];
}
