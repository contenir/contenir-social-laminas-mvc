<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\View;

use Contenir\Social\Exception\InvalidPageException;
use Contenir\Social\Share\Page;

use function parse_url;
use function str_starts_with;

use const PHP_URL_HOST;
use const PHP_URL_PORT;
use const PHP_URL_SCHEME;

/**
 * The page being shared, from what a template gives and, for the rest, the
 * page's own metadata. The address is og:url, else the canonical link, else
 * the current address; the title og:title, else the page's title; the
 * description og:description, else the description meta tag; the image
 * og:image. Addresses starting with / are made absolute on the current host.
 *
 * @api
 */
final readonly class PageSource
{
    public function __construct(
        private MetadataInterface $head,
        private ?string $currentUrl = null,
    ) {}

    /**
     * @param array{url?: string, title?: string, description?: string, image?: string} $given
     *
     * @throws InvalidPageException When the address can't be made absolute.
     */
    public function page(array $given = []): Page
    {
        $head = $this->head;

        return new Page(
            $this->absolute($given['url'] ?? $head->meta('og:url') ?? $head->canonical() ?? $this->currentUrl ?? ''),
            $given['title'] ?? $head->meta('og:title') ?? $head->title(),
            $given['description'] ?? $head->meta('og:description') ?? $head->meta('description') ?? '',
            $this->absoluteImage($given['image'] ?? $head->meta('og:image')),
        );
    }

    /**
     * $url, made absolute on the current host when it starts with /.
     */
    private function absolute(string $url): string
    {
        if (! str_starts_with($url, '/') || str_starts_with($url, '//') || null === $this->currentUrl) {
            return $url;
        }

        $scheme = parse_url($this->currentUrl, PHP_URL_SCHEME);
        $host   = parse_url($this->currentUrl, PHP_URL_HOST);
        $port   = parse_url($this->currentUrl, PHP_URL_PORT);

        return "{$scheme}://{$host}" . (null === $port ? '' : ":{$port}") . $url;
    }

    private function absoluteImage(?string $url): ?string
    {
        return null === $url ? null : $this->absolute($url);
    }
}
