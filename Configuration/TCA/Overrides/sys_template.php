<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

// Static TypoScript for installations without site sets (TYPO3 v12).
ExtensionManagementUtility::addStaticFile('feedivo', 'Configuration/TypoScript', 'Feedivo');
