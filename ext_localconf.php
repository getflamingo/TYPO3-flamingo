<?php
defined('TYPO3_MODE') or die;

if (TYPO3_MODE === 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers']['flamingo'] =
        \Ubermanu\Flamingo\Command\FlamingoCommandController::class;
}
