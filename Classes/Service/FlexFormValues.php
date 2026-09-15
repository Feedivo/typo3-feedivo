<?php

declare(strict_types=1);

namespace Feedivo\Typo3\Service;

use TYPO3\CMS\Core\Service\FlexFormService;

/**
 * Reads the feed of a content element from its FlexForm: the selected feed
 * wins, the manually entered ID is the fallback. Only a UUID-shaped value is
 * accepted — the value ends up in an HTML attribute the widget script reads.
 */
final class FlexFormValues
{
    public function __construct(private readonly FlexFormService $flexFormService) {}

    public function feedId(string $flexFormXml): string
    {
        if (trim($flexFormXml) === '') {
            return '';
        }

        return self::feedIdFromArray($this->flexFormService->convertFlexFormContentToArray($flexFormXml));
    }

    /** @param array<string, mixed> $values Flat FlexForm values (FlexFormProcessor output). */
    public static function feedIdFromArray(array $values): string
    {
        foreach (['feed', 'feedIdManual'] as $field) {
            $value = strtolower(trim((string) ($values[$field] ?? '')));
            if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $value) === 1) {
                return $value;
            }
        }

        return '';
    }
}
