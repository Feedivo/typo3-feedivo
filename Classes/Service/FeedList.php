<?php

declare(strict_types=1);

namespace Feedivo\Typo3\Service;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

/**
 * Feeds of the connection, cached for a few minutes (cache `feedivo_feeds`)
 * so the feed select and the page module preview do not call Feedivo on every
 * form render.
 */
final class FeedList
{
    private const CACHE_KEY = 'feeds';

    public function __construct(
        private readonly CacheManager $cacheManager,
        private readonly Connection $connection,
        private readonly FeedivoClient $client,
    ) {}

    /**
     * @return array<int, array{id: string, name: string}>
     * @throws FeedivoClientException
     */
    public function all(): array
    {
        $token = $this->connection->token();
        if ($token === '') {
            return [];
        }

        $cache = $this->cache();
        $cached = $cache->get(self::CACHE_KEY);
        if (is_array($cached)) {
            return $cached;
        }

        $feeds = $this->client->feeds($token);
        $cache->set(self::CACHE_KEY, $feeds);

        return $feeds;
    }

    public function nameOf(string $feedId): ?string
    {
        foreach ($this->all() as $feed) {
            if ($feed['id'] === $feedId) {
                return $feed['name'];
            }
        }

        return null;
    }

    public function flush(): void
    {
        $this->cache()->flush();
    }

    private function cache(): FrontendInterface
    {
        return $this->cacheManager->getCache('feedivo_feeds');
    }
}
