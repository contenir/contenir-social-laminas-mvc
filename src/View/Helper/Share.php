<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\View\Helper;

use Contenir\Social\Exception\InvalidPageException;
use Contenir\Social\Html\Options;
use Contenir\Social\Html\ShareButtons;
use Contenir\Social\Laminas\Mvc\HtmlOptions;
use Contenir\Social\Laminas\Mvc\NonceProviderInterface;
use Contenir\Social\Laminas\Mvc\View\PageSource;
use Contenir\Social\Script\ShareScript;
use Contenir\Social\Share\Page;
use Contenir\Social\Share\ShareConfig;
use Contenir\Social\Share\ShareLink;
use Contenir\Social\Share\ShareLinks;

use function array_filter;
use function is_array;
use function is_string;

/**
 * `$this->share()` in a template: the page's share buttons, built from the
 * page's own metadata (see PageSource), in the order the site chose in the
 * CMS. The first call on a page with "Copy link" or "Share…" also writes
 * the script behind them, with the page's CSP nonce.
 *
 *     <?= $this->share() ?>
 *     <?= $this->share(['title' => $entry->title], ['labels' => false]) ?>
 *
 * @api
 */
final class Share
{
    private bool $scriptWritten = false;

    public function __construct(
        private readonly ShareConfig $config,
        private readonly PageSource $pages,
        private readonly ?NonceProviderInterface $nonces = null,
        private readonly Options $defaults = new Options(),
    ) {}

    /**
     * The script for "Copy link" and "Share…", once per page: an empty
     * string after the first call. The nonce defaults to the application's
     * NonceProviderInterface.
     */
    public function script(?string $nonce = null): string
    {
        if ($this->scriptWritten) {
            return '';
        }

        $this->scriptWritten = true;

        return ShareScript::tag($nonce ?? $this->nonces?->nonce());
    }

    /**
     * The defaults, with names beside icons as the site chose in the CMS
     * unless the application's config says otherwise.
     */
    private function defaults(): Options
    {
        return new Options(
            icons: $this->defaults->icons,
            labels: $this->defaults->labels ?? $this->config->labels,
            newTab: $this->defaults->newTab,
            block: $this->defaults->block,
            class: $this->defaults->class,
        );
    }

    /**
     * The share buttons, then the script the first time a page needs it.
     *
     * @param Page|array{url?: string, title?: string, description?: string, image?: string}|null $page
     * @param Options|array<string, mixed> $options icons, labels, new_tab, block and class, plus script (false
     *                                              leaves the script out) and nonce
     *
     * @throws InvalidPageException When the page's address can't be made absolute.
     */
    public function __invoke(Page|array|null $page = null, Options|array $options = []): string
    {
        $links = (new ShareLinks($this->config))->for($page instanceof Page ? $page : $this->pages->page($page ?? []));
        $html  = ShareButtons::render($links, HtmlOptions::from($options, $this->defaults()));

        $settings = is_array($options) ? $options : [];
        $buttons  = array_filter($links, static fn(ShareLink $link): bool => $link->isButton());
        if ([] === $buttons || false === ($settings['script'] ?? true)) {
            return $html;
        }

        return $html . $this->script(is_string($settings['nonce'] ?? null) ? $settings['nonce'] : null);
    }
}
