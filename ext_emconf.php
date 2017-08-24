<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Flamingo',
    'description' => 'Task runner for data processing',
    'category' => 'be',
    'state' => 'beta',
    'version' => '0.7.0',
    'author' => 'ubermanu',
    'author_email' => '',
    'uploadFolder' => false,
    'clearCacheOnLoad' => false,
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-',
        ],
    ],
];
