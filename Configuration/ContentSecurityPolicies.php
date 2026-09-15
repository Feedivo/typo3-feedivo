<?php

declare(strict_types=1);

use Feedivo\Typo3\Service\ExtensionSettings;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Directive;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Mutation;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\MutationCollection;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\MutationMode;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Scope;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\UriValue;
use TYPO3\CMS\Core\Type\Map;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * Frontend policy for sites with TYPO3's Content Security Policy enabled: the
 * widget script, its stylesheet and the feed requests come from Feedivo, the
 * images and videos from Feedivo's media host, the YouTube player from
 * youtube-nocookie.com. Without these mutations an enabled CSP blocks the
 * feed silently.
 */
$origins = ['https://feedivo.de', 'https://cdn.feedivo.de'];
try {
    $origins = GeneralUtility::makeInstance(ExtensionSettings::class)->cspOrigins();
} catch (\Throwable) {
    // Defaults above; the extension configuration is not readable this early
    // in rare bootstrap situations.
}

$feedivo = array_map(static fn (string $origin): UriValue => new UriValue($origin), $origins);

return Map::fromEntries([
    Scope::frontend(),
    new MutationCollection(
        new Mutation(MutationMode::Extend, Directive::ScriptSrc, ...$feedivo),
        new Mutation(MutationMode::Extend, Directive::StyleSrc, ...$feedivo),
        new Mutation(MutationMode::Extend, Directive::ConnectSrc, ...$feedivo),
        new Mutation(MutationMode::Extend, Directive::ImgSrc, ...$feedivo),
        new Mutation(MutationMode::Extend, Directive::MediaSrc, ...$feedivo),
        new Mutation(MutationMode::Extend, Directive::FrameSrc, new UriValue('https://www.youtube-nocookie.com')),
    ),
]);
