<?php

declare(strict_types=1);

defined('TYPO3') or die();

// Feed list of the connection, fetched from Feedivo for the backend (feed
// select and preview). Short-lived: a feed renamed in Feedivo shows up here
// within minutes without a cache flush.
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['feedivo_feeds'] ??= [
    'options' => ['defaultLifetime' => 300],
    'groups' => ['system'],
];
