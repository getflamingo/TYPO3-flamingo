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
     * Execute a configured task
     *
     * @param string $filename
     * @param string $task
     * @param bool $debug
     * @param bool $force
     * @param bool $includeTypo3Configuration
     */
    public function runCommand(
        $filename,
        $task = 'default',
        $debug = false,
        $force = false,
        $includeTypo3Configuration = true
    ) {
        /** @var FlamingoService $flamingoService */
        $flamingoService = $this->objectManager->get(FlamingoService::class);

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
        $flamingoService->addConfiguration($filename, $includeTypo3Configuration);
        $flamingoService->parseConfiguration();
        $flamingoService->run($task);
    }
}
