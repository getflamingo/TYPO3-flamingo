<?php

namespace Ubermanu\Flamingo\Command;

use Analog\Analog;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use Ubermanu\Flamingo\Utility\ErrorUtility;
use Ubermanu\Flamingo\Utility\ExtensionUtility;

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
     * @param string $task
     */
    public function runCommand($task = 'default')
    {
        // Register logger using console messages
        Analog::handler(function ($error) {

            if (
                (false === ExtensionUtility::isDebugEnabled()) &&
                ($error['level'] === Analog::DEBUG)
            ) {
                return;
            }

            $this->outputLine(ErrorUtility::getMessage($error));

            if (
                (false === ExtensionUtility::isForceEnabled()) &&
                ($error['level'] == Analog::ERROR)
            ) {
                die;
            }
        });

        // Run specified task
        $this->flamingoService->run($task);
    }
}
