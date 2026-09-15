<?php

declare(strict_types=1);

namespace Feedivo\Typo3\Backend;

use Feedivo\Typo3\Service\Connection;
use Feedivo\Typo3\Service\FeedivoClientException;
use Feedivo\Typo3\Service\FeedList;
use Feedivo\Typo3\Service\FlexFormValues;
use TYPO3\CMS\Backend\View\Event\PageContentPreviewRenderingEvent;

/**
 * Preview of the content element in the page module: the selected feed by
 * name, or the reason why nothing will render.
 */
final class FeedPreview
{
    public function __construct(
        private readonly Connection $connection,
        private readonly FeedList $feedList,
        private readonly FlexFormValues $flexFormValues,
    ) {}

    public function __invoke(PageContentPreviewRenderingEvent $event): void
    {
        if ($event->getTable() !== 'tt_content') {
            return;
        }

        $row = self::rowOf($event->getRecord());
        if (($row['CType'] ?? '') !== 'feedivo_feed') {
            return;
        }

        $feedId = $this->flexFormValues->feedId((string) ($row['pi_flexform'] ?? ''));

        if (!$this->connection->isConnected()) {
            $event->setPreviewContent($this->note('preview.notConnected', 'warning'));
            return;
        }
        if ($feedId === '') {
            $event->setPreviewContent($this->note('preview.noFeed', 'warning'));
            return;
        }

        try {
            $name = $this->feedList->nameOf($feedId);
        } catch (FeedivoClientException) {
            $name = null;
        }

        $event->setPreviewContent(sprintf(
            '<p><strong>%s</strong> %s</p>',
            htmlspecialchars($this->label('preview.feed')),
            htmlspecialchars($name ?? $feedId),
        ));
    }

    /**
     * TYPO3 v12/v13 hand over the raw row, v14 a record object.
     *
     * @return array<string, mixed>
     */
    private static function rowOf(mixed $record): array
    {
        if (is_array($record)) {
            return $record;
        }
        if (is_object($record) && method_exists($record, 'getRawRecord')) {
            return $record->getRawRecord()->toArray();
        }
        if (is_object($record) && method_exists($record, 'toArray')) {
            return $record->toArray();
        }

        return [];
    }

    private function note(string $key, string $severity): string
    {
        return sprintf('<div class="alert alert-%s mb-0">%s</div>', $severity, htmlspecialchars($this->label($key)));
    }

    private function label(string $key): string
    {
        return (string) $GLOBALS['LANG']->sL('LLL:EXT:feedivo/Resources/Private/Language/locallang.xlf:' . $key);
    }
}
