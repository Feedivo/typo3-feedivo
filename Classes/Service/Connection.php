<?php

declare(strict_types=1);

namespace Feedivo\Typo3\Service;

use TYPO3\CMS\Core\Registry;

/**
 * The installation's connection to Feedivo, kept in the system registry
 * (database), never in a configuration file. The token stays in the backend
 * and the frontend rendering; only the public embed key reaches page output.
 *
 * Stored keys: token, embedKey, name, siteUrl, siteIdentifier, connectedAt.
 * The Feedivo connection ID itself is not kept: after the handshake it is no
 * longer needed, and it must not leak.
 */
final class Connection
{
    private const NAMESPACE = 'tx_feedivo';
    private const KEY = 'connection';

    public function __construct(private readonly Registry $registry) {}

    /** @return array<string, string>|null */
    public function get(): ?array
    {
        $value = $this->registry->get(self::NAMESPACE, self::KEY);

        return is_array($value) && ($value['token'] ?? '') !== '' ? $value : null;
    }

    public function isConnected(): bool
    {
        return $this->get() !== null;
    }

    public function token(): string
    {
        return (string) ($this->get()['token'] ?? '');
    }

    public function embedKey(): string
    {
        return (string) ($this->get()['embedKey'] ?? '');
    }

    /** @param array<string, string> $connection */
    public function store(array $connection): void
    {
        $this->registry->set(self::NAMESPACE, self::KEY, $connection);
    }

    public function clear(): void
    {
        $this->registry->remove(self::NAMESPACE, self::KEY);
    }
}
