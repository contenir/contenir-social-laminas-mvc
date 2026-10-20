<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Tests\Unit\View;

use Contenir\Social\Exception\InvalidPageException;
use Contenir\Social\Laminas\Mvc\Tests\TestAsset\FakeMetadata;
use Contenir\Social\Laminas\Mvc\View\PageSource;
use Contenir\Social\Share\Page;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class PageSourceTest extends TestCase
{
    private const string CURRENT = 'https://www.example.com/winners?page=2';

    /**
     * @return list<?string>
     */
    private static function values(Page $page): array
    {
        return [$page->url, $page->title, $page->description, $page->image];
    }

    #[Test]
    public function cannotMakeAnAddressAbsoluteWithoutTheCurrentOne(): void
    {
        $this->expectExceptionObject(InvalidPageException::notAbsolute('address', '/entry/1'));

        (new PageSource(new FakeMetadata()))->page(['url' => '/entry/1']);
    }

    #[Test]
    public function fallsBackToTheCanonicalLinkTitleAndDescription(): void
    {
        $source = new PageSource(
            new FakeMetadata(['description' => 'Who won'], 'https://www.example.com/winners', 'Winners | Houses'),
            self::CURRENT,
        );

        static::assertSame(
            ['https://www.example.com/winners', 'Winners | Houses', 'Who won', null],
            self::values($source->page()),
        );
    }

    #[Test]
    public function fallsBackToTheCurrentAddress(): void
    {
        $source = new PageSource(new FakeMetadata(), self::CURRENT);

        static::assertSame([self::CURRENT, '', '', null], self::values($source->page()));
    }

    #[Test]
    public function hasNoAddressAtAllWithoutMetadataOrTheCurrentOne(): void
    {
        $this->expectExceptionObject(InvalidPageException::notAbsolute('address', ''));

        (new PageSource(new FakeMetadata()))->page();
    }

    #[Test]
    public function leavesAProtocolRelativeAddressForPageToRefuse(): void
    {
        $this->expectExceptionObject(InvalidPageException::notAbsolute('address', '//cdn.example.com/x'));

        (new PageSource(new FakeMetadata(), self::CURRENT))->page(['url' => '//cdn.example.com/x']);
    }

    #[Test]
    public function makesAddressesFromTheRootAbsoluteOnTheCurrentHost(): void
    {
        $source = new PageSource(new FakeMetadata(['og:image' => '/a.jpg']), 'http://localhost:8080/x');

        static::assertSame(
            ['http://localhost:8080/entry/1', 'http://localhost:8080/a.jpg'],
            [$source->page(['url' => '/entry/1'])->url, $source->page()->image],
        );
    }

    #[Test]
    public function readsTheOpenGraphTags(): void
    {
        $source = new PageSource(
            new FakeMetadata(
                [
                    'og:url'         => 'https://www.example.com/winners',
                    'og:title'       => 'The winners',
                    'og:description' => 'Who won',
                    'og:image'       => 'https://www.example.com/a.jpg',
                    'description'    => 'Not this',
                ],
                'https://www.example.com/not-this',
                'Not this either',
            ),
            self::CURRENT,
        );

        static::assertSame(
            ['https://www.example.com/winners', 'The winners', 'Who won', 'https://www.example.com/a.jpg'],
            self::values($source->page()),
        );
    }

    #[Test]
    public function takesWhatTheTemplateGivesFirst(): void
    {
        $source = new PageSource(
            new FakeMetadata(['og:title' => 'Not this', 'og:description' => 'Not this', 'og:image' => 'Not this']),
            self::CURRENT,
        );

        static::assertSame(
            ['https://www.example.com/entry/1', 'Entry 1', 'An entry', 'https://www.example.com/1.jpg'],
            self::values($source->page([
                'url'         => 'https://www.example.com/entry/1',
                'title'       => 'Entry 1',
                'description' => 'An entry',
                'image'       => 'https://www.example.com/1.jpg',
            ])),
        );
    }
}
