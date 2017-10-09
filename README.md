# TYPO3 Flamingo

Extension that gives TYPO3 environment support to [Flamingo](https://github.com/ubermanu/flamingo).<br>
If you are new to flamingo, check out the [wiki](https://github.com/ubermanu/flamingo/wiki) available on github.

### Configuration

Add your YML files in your extension in the folder **Configuration/Flamingo/**.<br>
Then you can register your configuration files. Example:

    plugin.tx_flamingo {
        settings {
            yamlConfigurations {
                10: EXT:example/Configuration/Yaml/Example.yaml
            }
        }
    }

### Variables

More references will be implemented in the future (FAL will be first).<br>
For the moment only *TYPO3_DB* is available.

###### TYPO3_DB

Contains an array of configuration compatible for database sources and destinations.<br>
Example:

    Flamingo/Task/Default:
      - Src:
        - <<: *TYPO3_DB
          table: sys_domain
      - Dest:

> This configuration will output all the sys_domain rows from default DB.

### Helpers

You can add custom helpers in your **Classes/Helper/** folder.<br>
In the near future, configuration will be passed into user functions, so it become more customizable.

    class Test implements \Ubermanu\Flamingo\UserFunction\UserFunctionInterface
    {
        public function run(array $configuration, TaskRuntime $taskRuntime)
        {
            foreach ($taskRuntime->getTableByIdentifier(0) as $row) {
                echo $row['title'];
            }
        }
    }

> This helper will output the titles of each rows, see the documentation for more information.

### CLI

You can run a task using typo3cms or the cli_dispatch script.

    php typo3cms flamingo:run test

> This command will run the "test" task defined in an included file.
