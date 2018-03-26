# TYPO3 Flamingo

Extension that gives TYPO3 environment support to [Flamingo](https://github.com/ubermanu/flamingo).<br>
If you are new to flamingo, check out the [wiki](https://github.com/ubermanu/flamingo/wiki) available on github.

### Task

You can add custom task in your **Classes/Flamingo/** folder.

    class TestTask extends \Ubermanu\Flamingo\Core\AbstractTask
    {
        public function __invoke()
        {
            $filename = 'EXT:my_ext/Resources/Private/File.csv';
            $data = $this->read(GeneralUtility::getFileAbsFileName($filename));
        }
    }

> This example loads a basic CSV file.

### CLI

You can run a task using typo3cms or the cli_dispatch script.

    php typo3cms flamingo:run \Vendor\MyExt\Flamingo\TestTask

> This command will run the TestTask class.
