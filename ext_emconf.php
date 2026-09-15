<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Feedivo – Social Media Feeds',
    'description' => 'Show your Instagram, Facebook, Threads, Pinterest and YouTube posts from Feedivo as a content element.',
    'category' => 'plugin',
    'author' => 'Feedivo',
    'author_company' => 'Feedivo',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-14.99.99',
            'php' => '8.1.0-8.5.99',
            'fluid_styled_content' => '12.4.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
