<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\View;

/**
 * The metadata a page has already written for its head.
 *
 * @api
 */
interface MetadataInterface
{
    /**
     * The canonical link's address, or null when the page has none.
     */
    public function canonical(): ?string;

    /**
     * A meta tag's content, by name (description) or property (og:title),
     * or null when the page has none.
     */
    public function meta(string $key): ?string;

    /**
     * The page's title as the browser shows it, or an empty string.
     */
    public function title(): string;
}
