<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Tests\Integration;

use Contenir\Social\Laminas\Mvc\Tests\Trait\RendersViewsTrait;
use Contenir\Social\Laminas\Mvc\View\HeadMetadata;
use Laminas\View\Helper\HeadLink;
use Laminas\View\Helper\HeadMeta;
use Laminas\View\Helper\HeadTitle;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Reading the metadata laminas-view's head helpers hold, as
 * contenir-resource's resourceMeta() and templates write it.
 */
#[Group('integration')]
final class HeadMetadataTest extends TestCase
{
    use RendersViewsTrait;

    #[Test]
    public function hasNoCanonicalLinkUnlessThePageHasOne(): void
    {
        $this->renderer->headLink(['rel' => 'stylesheet', 'href' => '/site.css']);

        static::assertNull($this->metadata()->canonical());
    }

    #[Test]
    public function readsAnEmptyTagAsNone(): void
    {
        $this->renderer->headMeta()->setProperty('og:title', ' ');

        static::assertNull($this->metadata()->meta('og:title'));
    }

    #[Test]
    public function readsAPropertyAndANameEachByItsKey(): void
    {
        $this->renderer->headMeta()->setName('description', ' Who won ');
        $this->renderer->headMeta()->setProperty('og:description', 'og:title');
        $this->renderer->headMeta()->setProperty('og:title', 'The winners');

        static::assertSame(
            ['Who won', 'The winners', 'og:title', null],
            [
                $this->metadata()->meta('description'),
                $this->metadata()->meta('og:title'),
                $this->metadata()->meta('og:description'),
                $this->metadata()->meta('og:image'),
            ],
        );
    }

    #[Test]
    public function readsQuotesInTheTitleAndTrimsIt(): void
    {
        $this->renderer->headTitle(' The "best" houses ');

        static::assertSame('The "best" houses', $this->metadata()->title());
    }

    #[Test]
    public function readsTheCanonicalLinkAmongOthers(): void
    {
        $this->renderer->headLink(['rel' => 'stylesheet', 'href' => '/site.css']);
        $this->renderer->headLink(['rel' => 'canonical', 'href' => 'https://www.example.com/winners']);

        static::assertSame('https://www.example.com/winners', $this->metadata()->canonical());
    }

    #[Test]
    public function readsTheTitleAsTheBrowserShowsIt(): void
    {
        $this->renderer->headTitle('Winners & <commended>')->setSeparator(' | ')->append('Houses');

        static::assertSame('Winners & <commended> | Houses', $this->metadata()->title());
    }

    #[Override]
    protected function setUp(): void
    {
        $this->setUpViews([]);
    }

    private function metadata(): HeadMetadata
    {
        return new HeadMetadata(
            $this->renderer->plugin(HeadMeta::class),
            $this->renderer->plugin(HeadLink::class),
            $this->renderer->plugin(HeadTitle::class),
        );
    }
}
