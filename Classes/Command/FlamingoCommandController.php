<?php

namespace Ubermanu\Flamingo\Command;

use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use Ubermanu\Flamingo\Service\FlamingoService;
use Ubermanu\Flamingo\Utility\ErrorUtility;

/**
 * Class FlamingoCommandController
 * @package Ubermanu\Flamingo\Command
 */
class FlamingoCommandController extends CommandController
{
    /**
     * Execute a configured task.
     *
     * @param string $task
     * @param bool $debug
     * @param bool $force
     */
    public function runCommand($task, $debug = false, $force = false)
    {
        $flamingoService = FlamingoService::getInstance();

        // Register logger using console messages
        \Analog\Analog::handler(function ($error) use ($debug, $force) {

            // Skip debug
            if ($debug === false && $error['level'] === \Analog\Analog::DEBUG) {
                return;
            }

            // Output current log
            $this->outputLine(ErrorUtility::getMessage($error));

            // The log is an error, stop
            if ($force === false && $error['level'] === \Analog\Analog::ERROR) {
                $this->sendAndExit(\Analog\Analog::ERROR);
            }
        });

        // Run specified task
        $flamingoService->run($task);
    }
}
