<?php

declare(strict_types=1);

namespace Feedivo\Typo3;

/**
 * Extension identity. The version is reported to Feedivo with every request
 * (X-Feedivo-Client) and must match ext_emconf.php and composer.json; the
 * build script checks all three.
 */
final class Extension
{
    public const KEY = 'feedivo';
    public const VERSION = '1.0.0';

    public static function client(): string
    {
        return 'typo3/' . self::VERSION;
    }
}
