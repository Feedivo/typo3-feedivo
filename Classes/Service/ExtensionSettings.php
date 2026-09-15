<?php

declare(strict_types=1);

namespace Feedivo\Typo3\Service;

use Feedivo\Typo3\Extension;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * Typed access to the extension configuration (Admin Tools > Settings).
 * Missing or unreadable values fall back to the production defaults.
 */
final class ExtensionSettings
{
    private const DEFAULT_BASE_URL = 'https://feedivo.de';
    private const DEFAULT_ADDITIONAL_HOSTS = 'https://cdn.feedivo.de';

    /** @var array<string, mixed>|null */
    private ?array $values = null;

    public function __construct(private readonly ExtensionConfiguration $extensionConfiguration) {}

    public function baseUrl(): string
    {
        $url = trim((string) ($this->values()['baseUrl'] ?? ''));
        if ($url === '' || !preg_match('#^https?://#i', $url)) {
            $url = self::DEFAULT_BASE_URL;
        }

        return rtrim($url, '/');
    }

    public function scriptUrl(): string
    {
        return $this->baseUrl() . '/assets/js/widget.js';
    }

    /** @return array<int, string> Origins for the Content Security Policy, base URL included. */
    public function cspOrigins(): array
    {
        $raw = (string) ($this->values()['additionalHosts'] ?? self::DEFAULT_ADDITIONAL_HOSTS);
        $origins = [$this->baseUrl()];
        foreach (explode(',', $raw) as $origin) {
            $origin = rtrim(trim($origin), '/');
            if ($origin !== '' && preg_match('#^https?://[a-z0-9.-]+(:\d+)?$#i', $origin)) {
                $origins[] = $origin;
            }
        }

        return array_values(array_unique($origins));
    }

    public function verifyTls(): bool
    {
        return (bool) ($this->values()['verifyTls'] ?? true);
    }

    /** @return array<string, mixed> */
    private function values(): array
    {
        if ($this->values === null) {
            try {
                $values = $this->extensionConfiguration->get(Extension::KEY);
                $this->values = is_array($values) ? $values : [];
            } catch (\Throwable) {
                $this->values = [];
            }
        }

        return $this->values;
    }
}
