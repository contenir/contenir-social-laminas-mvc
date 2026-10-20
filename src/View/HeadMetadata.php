<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\View;

use Laminas\View\Helper\HeadLink;
use Laminas\View\Helper\HeadMeta;
use Laminas\View\Helper\HeadTitle;
use Override;

use function get_object_vars;
use function html_entity_decode;
use function is_string;
use function strip_tags;
use function trim;

use const ENT_HTML5;
use const ENT_QUOTES;

/**
 * The page's metadata as the layout's head helpers hold it: the Open Graph
 * and description meta tags, the canonical link and the title, which
 * contenir-resource's resourceMeta() and a site's own templates write.
 *
 * @api
 */
final readonly class HeadMetadata implements MetadataInterface
{
    public function __construct(
        private HeadMeta $meta,
        private HeadLink $links,
        private HeadTitle $title,
    ) {}

    private static function filled(string $value): ?string
    {
        $value = trim($value);

        return '' === $value ? null : $value;
    }

    /**
     * The canonical link's address, or null when the page has none.
     */
    #[Override]
    public function canonical(): ?string
    {
        foreach ($this->links as $link) {
            $attributes = get_object_vars($link);
            if ('canonical' === ($attributes['rel'] ?? null) && is_string($attributes['href'] ?? null)) {
                return self::filled($attributes['href']);
            }
        }

        return null;
    }

    /**
     * A meta tag's content, by name (description) or property (og:title),
     * or null when the page has none.
     *
     * @mago-expect analysis:mixed-assignment A meta item's attributes are untyped; each is checked here.
     */
    #[Override]
    public function meta(string $key): ?string
    {
        foreach ($this->meta as $item) {
            $attributes = get_object_vars($item);
            $type       = $attributes['type'] ?? null;
            if (
                is_string($type)
                && $key === ($attributes[$type] ?? null)
                && is_string($attributes['content'] ?? null)
            ) {
                return self::filled($attributes['content']);
            }
        }

        return null;
    }

    /**
     * The page's title as the browser shows it, or an empty string.
     */
    #[Override]
    public function title(): string
    {
        return trim(html_entity_decode(
            strip_tags($this->title->renderTitle()),
            ENT_QUOTES | ENT_HTML5,
            encoding: 'UTF-8',
        ));
    }
}
