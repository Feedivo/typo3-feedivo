<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

$cType = 'feedivo_feed';

ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        'label' => 'LLL:EXT:feedivo/Resources/Private/Language/locallang_db.xlf:ctype.feedivo_feed.title',
        'description' => 'LLL:EXT:feedivo/Resources/Private/Language/locallang_db.xlf:ctype.feedivo_feed.description',
        'value' => $cType,
        'icon' => 'feedivo-feed',
        'group' => 'special',
    ],
);

$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes'][$cType] = 'feedivo-feed';

$GLOBALS['TCA']['tt_content']['types'][$cType] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,
            --palette--;;headers,
            pi_flexform,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
            --palette--;;language,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,
            --palette--;;access,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
            categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
            rowDescription,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
    ',
    'columnsOverrides' => [
        'pi_flexform' => [
            'label' => 'LLL:EXT:feedivo/Resources/Private/Language/locallang_db.xlf:flexform.label',
        ],
    ],
];

// The FlexForm is attached per TYPO3 major version: v14 takes a single data
// structure in columnsOverrides, v12/v13 the "*,<CType>" entry of the ds
// array. Neither form works on the other side.
$flexForm = 'FILE:EXT:feedivo/Configuration/FlexForms/Feed.xml';
if ((new Typo3Version())->getMajorVersion() >= 14) {
    $GLOBALS['TCA']['tt_content']['types'][$cType]['columnsOverrides']['pi_flexform']['config']['ds'] = $flexForm;
} else {
    $GLOBALS['TCA']['tt_content']['columns']['pi_flexform']['config']['ds']['*,' . $cType] = $flexForm;
}
