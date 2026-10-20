<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Tests\TestAsset;

use Contenir\Social\Laminas\Mvc\View\MetadataInterface;
use Override;

/**
 * A page's head metadata, held in arrays.
 */
final readonly class FakeMetadata implements MetadataInterface
{
    /**
     * @param array<string, string> $meta By name or property
     */
    public function __construct(
        private array $meta = [],
        private ?string $canonical = null,
        private string $title = '',
    ) {}

    #[Override]
    public function canonical(): ?string
    {
        return $this->canonical;
    }

    #[Override]
    public function meta(string $key): ?string
    {
        return $this->meta[$key] ?? null;
    }

    #[Override]
    public function title(): string
    {
        return $this->title;
    }
}
