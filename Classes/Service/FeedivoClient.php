<?php

declare(strict_types=1);

namespace Feedivo\Typo3\Service;

use Feedivo\Typo3\Extension;
use TYPO3\CMS\Core\Http\RequestFactory;

/**
 * The three calls the extension makes to Feedivo: handshake (bind this site
 * with a connection ID, get the token and the public embed key), feed list
 * (with the token) and disconnect. Every response is the Feedivo envelope
 * {data, meta, errors}.
 */
final class FeedivoClient
{
    private const TIMEOUT = 15;

    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly ExtensionSettings $settings,
    ) {}

    /**
     * @return array{token: string, embedKey: string, name: string, feeds: array<int, array{id: string, name: string}>}
     */
    public function handshake(string $integrationId, string $siteUrl): array
    {
        $data = $this->request('POST', '/api/v1/typo3/handshake', [
            'json' => [
                'integration_id' => $integrationId,
                'site_url' => $siteUrl,
                'plugin_version' => Extension::VERSION,
            ],
        ]);

        $result = $data['results'][0] ?? null;
        if (!is_array($result) || ($result['token'] ?? '') === '' || ($result['embed_key'] ?? '') === '') {
            throw new FeedivoClientException('transient', 'Feedivo answered without a token. Please try again.');
        }

        return [
            'token' => (string) $result['token'],
            'embedKey' => (string) $result['embed_key'],
            'name' => (string) ($result['integration']['name'] ?? ''),
            'feeds' => self::feedList((array) ($result['feeds'] ?? [])),
        ];
    }

    /** @return array<int, array{id: string, name: string}> */
    public function feeds(string $token): array
    {
        $data = $this->request('GET', '/api/v1/feeds', ['headers' => $this->authHeaders($token)]);

        return self::feedList(is_array($data) ? $data : []);
    }

    public function disconnect(string $token): void
    {
        $this->request('POST', '/api/v1/typo3/disconnect', ['headers' => $this->authHeaders($token)]);
    }

    /** @return array<string, string> */
    private function authHeaders(string $token): array
    {
        return ['Authorization' => 'Bearer ' . $token];
    }

    /**
     * @param array<string, mixed> $options
     * @return mixed The envelope's `data`
     */
    private function request(string $method, string $path, array $options)
    {
        $options['headers'] = ($options['headers'] ?? []) + [
            'Accept' => 'application/json',
            'X-Feedivo-Client' => Extension::client(),
        ];
        $options['timeout'] = self::TIMEOUT;
        $options['http_errors'] = false;
        $options['verify'] = $this->settings->verifyTls();

        try {
            $response = $this->requestFactory->request($this->settings->baseUrl() . $path, $method, $options);
        } catch (\Throwable $e) {
            throw new FeedivoClientException('transient', 'Feedivo could not be reached: ' . $e->getMessage());
        }

        $status = $response->getStatusCode();
        $body = json_decode((string) $response->getBody(), true);
        if (!is_array($body)) {
            throw new FeedivoClientException('transient', 'Feedivo answered with an unreadable response (HTTP ' . $status . ').');
        }

        if ($status >= 200 && $status < 300) {
            return $body['data'] ?? null;
        }

        $error = $body['errors'][0] ?? [];
        $message = (string) ($error['message'] ?? ('HTTP ' . $status));
        $kind = match (true) {
            $status === 401 => 'auth',
            $status === 403 && ($error['code'] ?? '') === 'entitlement_paused' => 'paused',
            $status === 404 => 'not_found',
            $status === 422 => 'invalid',
            default => 'transient',
        };

        throw new FeedivoClientException($kind, $message);
    }

    /**
     * @param array<int, mixed> $rows
     * @return array<int, array{id: string, name: string}>
     */
    private static function feedList(array $rows): array
    {
        $feeds = [];
        foreach ($rows as $row) {
            if (is_array($row) && ($row['id'] ?? '') !== '') {
                $feeds[] = ['id' => (string) $row['id'], 'name' => (string) ($row['name'] ?? $row['id'])];
            }
        }

        return $feeds;
    }
}
