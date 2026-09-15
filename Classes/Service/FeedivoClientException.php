<?php

declare(strict_types=1);

namespace Feedivo\Typo3\Service;

/**
 * A failed call to Feedivo with a user-facing message. `kind` lets callers
 * react: `auth` (token revoked), `paused` (plan paused), `not_found`
 * (unknown connection ID), `invalid` (rejected input), `transient` (network,
 * 5xx, unreadable answer).
 */
final class FeedivoClientException extends \RuntimeException
{
    public function __construct(public readonly string $kind, string $message)
    {
        parent::__construct($message);
    }
}
