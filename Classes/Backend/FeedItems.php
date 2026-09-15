<?php

declare(strict_types=1);

namespace Feedivo\Typo3\Backend;

use Feedivo\Typo3\Service\Connection;
use Feedivo\Typo3\Service\FeedivoClientException;
use Feedivo\Typo3\Service\FeedList;

/**
 * itemsProcFunc of the feed select in the content element: the connection's
 * feeds by name. Without a connection or with Feedivo unreachable the select
 * carries one explanatory entry instead of failing the form.
 */
final class FeedItems
{
    public function __construct(
        private readonly Connection $connection,
        private readonly FeedList $feedList,
    ) {}

    /** @param array<string, mixed> $params */
    public function items(array &$params): void
    {
        if (!$this->connection->isConnected()) {
            $params['items'][] = ['label' => $this->label('feed.notConnected'), 'value' => '--div--'];
            return;
        }

        try {
            $feeds = $this->feedList->all();
        } catch (FeedivoClientException $e) {
            $params['items'][] = ['label' => sprintf($this->label('feed.unavailable'), $e->getMessage()), 'value' => '--div--'];
            return;
        }

        if ($feeds === []) {
            $params['items'][] = ['label' => $this->label('feed.empty'), 'value' => '--div--'];
            return;
        }

        foreach ($feeds as $feed) {
            $params['items'][] = ['label' => $feed['name'], 'value' => $feed['id']];
        }
    }

    private function label(string $key): string
    {
        return (string) $GLOBALS['LANG']->sL('LLL:EXT:feedivo/Resources/Private/Language/locallang.xlf:' . $key);
    }
}
