<?php

declare(strict_types=1);

use Feedivo\Typo3\Controller\ConnectionController;

/**
 * Backend module "Feedivo" under Site Management: connect the installation
 * with a Feedivo connection ID, see the connected feeds, disconnect.
 */
return [
    'site_feedivo' => [
        'parent' => 'site',
        'position' => ['after' => 'site_configuration'],
        'access' => 'admin',
        'workspaces' => 'live',
        'path' => '/module/site/feedivo',
        'iconIdentifier' => 'feedivo-module',
        'labels' => 'LLL:EXT:feedivo/Resources/Private/Language/locallang_mod.xlf',
        'routes' => [
            '_default' => [
                'target' => ConnectionController::class . '::handleRequest',
            ],
        ],
    ],
];
