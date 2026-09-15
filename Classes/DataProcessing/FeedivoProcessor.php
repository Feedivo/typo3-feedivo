<?php

declare(strict_types=1);

namespace Feedivo\Typo3\DataProcessing;

use Feedivo\Typo3\Service\Connection;
use Feedivo\Typo3\Service\ExtensionSettings;
use Feedivo\Typo3\Service\FlexFormValues;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Gives the Fluid template what the widget container needs: the public embed
 * key of the connection and the feed of the element. The token never reaches
 * the template. Adds the widget script through the AssetCollector (once per
 * page, async, without CSP hash collection — the script is an external file).
 */
final class FeedivoProcessor implements DataProcessorInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly ExtensionSettings $settings,
        private readonly FlexFormValues $flexFormValues,
        private readonly AssetCollector $assetCollector,
    ) {}

    /**
     * @param array<string, mixed> $contentObjectConfiguration
     * @param array<string, mixed> $processorConfiguration
     * @param array<string, mixed> $processedData
     * @return array<string, mixed>
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData,
    ): array {
        $as = (string) $cObj->stdWrapValue('as', $processorConfiguration, 'feedivo');

        $flexForm = $processedData['flexform'] ?? null;
        $feedId = is_array($flexForm)
            ? FlexFormValues::feedIdFromArray($flexForm)
            : $this->flexFormValues->feedId((string) ($processedData['data']['pi_flexform'] ?? ''));

        $embedKey = $this->connection->embedKey();
        if ($embedKey !== '' && $feedId !== '') {
            $this->assetCollector->addJavaScript(
                'feedivo-widget',
                $this->settings->scriptUrl(),
                ['async' => 'async'],
                ['csp' => false],
            );
        }

        $processedData[$as] = [
            'connected' => $this->connection->isConnected(),
            'embedKey' => $embedKey,
            'feedId' => $feedId,
        ];

        return $processedData;
    }
}
