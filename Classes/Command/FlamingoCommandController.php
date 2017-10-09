<?php

namespace Ubermanu\Flamingo\Command;

use Analog\Analog;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use Ubermanu\Flamingo\Utility\ErrorUtility;

/**
 * Class FlamingoCommandController
 * @package Ubermanu\Flamingo\Command
 */
class FlamingoCommandController extends CommandController
{
    /**
     * @var \Ubermanu\Flamingo\Service\FlamingoService
     * @inject
     */
    protected $flamingoService;

    /**
     * Execute a configured task
     *
     * @param string $task
     * @param bool $debug
     * @param bool $force
     */
    public function runCommand($task = 'default', $debug = false, $force = false)
    {
        // Register logger using console messages
        Analog::handler(function ($error) use ($debug, $force) {

            // Skip debug
            if ($debug === false && $error['level'] === Analog::DEBUG) {
                return;
            }

            // Output current log
            $this->outputLine(ErrorUtility::getMessage($error));

            // The log is an error, stop
            if ($force === false && $error['level'] === Analog::ERROR) {
                $this->sendAndExit(Analog::ERROR);
            }
        });

        // Run specified task
        $this->flamingoService->parseConfiguration();
        $this->flamingoService->run($task);
    }

    /**
     * Current flamingo version
     * @cli
     */
    public function versionCommand()
    {
        $this->flamingoService->parseConfiguration();
        $this->outputLine($GLOBALS['FLAMINGO']['Version']);
    }
}
