<?php

namespace Ubermanu\Flamingo\Command;

use Analog\Analog;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use Ubermanu\Flamingo\Utility\ConfigurationUtility;
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
     * @param string $task
     */
    public function runCommand($task = 'default')
    {
        // Register logger using console messages
        Analog::handler(function ($error) {

            if (
                (false === ConfigurationUtility::isDebugEnabled()) &&
                ($error['level'] === Analog::DEBUG)
            ) {
                return;
            }

            $this->outputLine(ErrorUtility::getMessage($error));

            if (
                (false === ConfigurationUtility::isForceEnabled()) &&
                ($error['level'] == Analog::ERROR)
            ) {
                die;
            }
        });

        // Run specified task
        $this->flamingoService->run($task);
    }

    /**
     * Current flamingo version
     * @cli
     */
    public function versionCommand()
    {
        $this->outputLine($GLOBALS['FLAMINGO']['CONF']['App']['Version']);
    }
}
