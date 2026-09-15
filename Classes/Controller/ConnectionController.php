<?php

declare(strict_types=1);

namespace Feedivo\Typo3\Controller;

use Feedivo\Typo3\Extension;
use Feedivo\Typo3\Service\Connection;
use Feedivo\Typo3\Service\ExtensionSettings;
use Feedivo\Typo3\Service\FeedivoClient;
use Feedivo\Typo3\Service\FeedivoClientException;
use Feedivo\Typo3\Service\FeedList;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;

/**
 * Backend module "Feedivo" (Site Management): enter the connection ID from
 * the Feedivo account, connect, see the feeds, disconnect. One connection per
 * installation. The POST actions arrive on the module route, which carries
 * the backend's request token.
 */
final class ConnectionController
{
    private const ROUTE = 'site_feedivo';

    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly UriBuilder $uriBuilder,
        private readonly SiteFinder $siteFinder,
        private readonly Connection $connection,
        private readonly FeedivoClient $client,
        private readonly FeedList $feedList,
        private readonly ExtensionSettings $settings,
    ) {}

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $view = $this->moduleTemplateFactory->create($request);
        $view->setTitle($this->label('mlang_tabs_tab', 'locallang_mod'));

        if (strtoupper($request->getMethod()) === 'POST') {
            $body = (array) $request->getParsedBody();
            $action = (string) ($body['action'] ?? '');

            if ($action === 'connect') {
                $this->connect($request, $view, $body);
            } elseif ($action === 'disconnect') {
                $this->disconnect($view);
            }

            return new RedirectResponse((string) $this->uriBuilder->buildUriFromRoute(self::ROUTE), 303);
        }

        return $this->index($view);
    }

    private function index(ModuleTemplate $view): ResponseInterface
    {
        $connection = $this->connection->get();
        $feeds = [];
        $feedError = null;
        if ($connection !== null) {
            try {
                $feeds = $this->feedList->all();
            } catch (FeedivoClientException $e) {
                $feedError = $e->getMessage();
            }
        }

        $sites = [];
        foreach ($this->siteFinder->getAllSites() as $site) {
            $sites[$site->getIdentifier()] = (string) $site->getBase();
        }

        $view->assignMultiple([
            'connection' => $connection,
            'feeds' => $feeds,
            'feedError' => $feedError,
            'sites' => $sites,
            'baseUrl' => $this->settings->baseUrl(),
            'version' => Extension::VERSION,
            'formAction' => (string) $this->uriBuilder->buildUriFromRoute(self::ROUTE),
        ]);

        return $view->renderResponse('Connection/Index');
    }

    /** @param array<string, mixed> $body */
    private function connect(ServerRequestInterface $request, ModuleTemplate $view, array $body): void
    {
        $integrationId = trim((string) ($body['integrationId'] ?? ''));
        if (preg_match('/^[0-9a-fA-F-]{8,64}$/', $integrationId) !== 1) {
            $this->flash($view, 'message.invalidId', ContextualFeedbackSeverity::ERROR);
            return;
        }

        $siteIdentifier = (string) ($body['site'] ?? '');
        $siteUrl = $this->siteUrl($request, $siteIdentifier);

        try {
            $result = $this->client->handshake($integrationId, $siteUrl);
        } catch (FeedivoClientException $e) {
            $key = $e->kind === 'not_found' ? 'message.unknownId' : 'message.failed';
            $this->flash($view, $key, ContextualFeedbackSeverity::ERROR, $e->getMessage());
            return;
        }

        $this->connection->store([
            'token' => $result['token'],
            'embedKey' => $result['embedKey'],
            'name' => $result['name'],
            'siteUrl' => $siteUrl,
            'siteIdentifier' => $siteIdentifier,
            'connectedAt' => date('c'),
        ]);
        $this->feedList->flush();

        $this->flash($view, 'message.connected', ContextualFeedbackSeverity::OK, $result['name']);
    }

    private function disconnect(ModuleTemplate $view): void
    {
        $token = $this->connection->token();
        if ($token !== '') {
            try {
                $this->client->disconnect($token);
            } catch (FeedivoClientException) {
                // A revoked or unreachable token changes nothing: the local
                // connection is removed either way, Feedivo shows the
                // connection as pending once the site stops calling.
            }
        }

        $this->connection->clear();
        $this->feedList->flush();
        $this->flash($view, 'message.disconnected', ContextualFeedbackSeverity::INFO);
    }

    /**
     * Public address of the chosen site; a site without an absolute base
     * (e.g. "/") falls back to the address the backend was called with.
     */
    private function siteUrl(ServerRequestInterface $request, string $siteIdentifier): string
    {
        $base = '';
        try {
            $base = (string) $this->siteFinder->getSiteByIdentifier($siteIdentifier)->getBase();
        } catch (\Throwable) {
            // Unknown identifier: fall through to the request's own address.
        }

        if (preg_match('#^https?://#i', $base) !== 1) {
            $params = $request->getAttribute('normalizedParams');
            $base = $params !== null ? $params->getSiteUrl() : '';
        }

        return rtrim($base, '/');
    }

    private function flash(ModuleTemplate $view, string $key, ContextualFeedbackSeverity $severity, string $detail = ''): void
    {
        $text = $this->label($key);
        if ($detail !== '') {
            $text = sprintf($text, $detail);
        }
        $view->addFlashMessage($text, '', $severity);
    }

    private function label(string $key, string $file = 'locallang'): string
    {
        return (string) $GLOBALS['LANG']->sL('LLL:EXT:feedivo/Resources/Private/Language/' . $file . '.xlf:' . $key);
    }
}
