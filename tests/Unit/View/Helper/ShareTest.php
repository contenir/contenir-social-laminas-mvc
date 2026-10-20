<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Tests\Unit\View\Helper;

use Contenir\Social\Html\Options;
use Contenir\Social\Html\ShareButtons;
use Contenir\Social\Laminas\Mvc\Tests\TestAsset\FakeMetadata;
use Contenir\Social\Laminas\Mvc\Tests\TestAsset\FixedNonce;
use Contenir\Social\Laminas\Mvc\View\Helper\Share;
use Contenir\Social\Laminas\Mvc\View\PageSource;
use Contenir\Social\Script\ShareScript;
use Contenir\Social\Share\Page;
use Contenir\Social\Share\ShareConfig;
use Contenir\Social\Share\ShareLinks;
use Contenir\Social\Share\ShareTarget;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class ShareTest extends TestCase
{
    private const string URL = 'https://www.example.com/winners';

    /**
     * @param list<ShareTarget> $targets
     */
    private static function buttons(array $targets, Options $options, ?Page $page = null): string
    {
        $config = new ShareConfig($targets);

        return ShareButtons::render((new ShareLinks($config))->for($page ?? new Page(self::URL, 'Winners')), $options);
    }

    private static function source(): PageSource
    {
        return new PageSource(new FakeMetadata(['og:url' => self::URL, 'og:title' => 'Winners']));
    }

    #[Test]
    public function leavesTheScriptOutWhenAsked(): void
    {
        $helper = new Share(new ShareConfig([ShareTarget::CopyLink]), self::source());

        static::assertSame(
            [self::buttons([ShareTarget::CopyLink], new Options()), ShareScript::tag()],
            [$helper(null, ['script' => false]), $helper->script()],
        );
    }

    #[Test]
    public function sharesThePageDescribedByTheTemplate(): void
    {
        $helper = new Share(new ShareConfig([ShareTarget::X], labels: false), self::source());

        static::assertSame(
            self::buttons([ShareTarget::X], new Options(labels: false), new Page('https://www.example.com/1', 'One')),
            $helper(['url' => 'https://www.example.com/1', 'title' => 'One']),
        );
    }

    #[Test]
    public function sharesThePageItIsGiven(): void
    {
        $helper = new Share(new ShareConfig([ShareTarget::X], labels: false), self::source());
        $page   = new Page('https://www.example.com/entry/1', 'Entry 1');

        static::assertSame(self::buttons([ShareTarget::X], new Options(labels: false), $page), $helper($page));
    }

    #[Test]
    public function takesOptionsAsTheyAre(): void
    {
        $helper  = new Share(new ShareConfig([ShareTarget::CopyLink]), self::source());
        $options = new Options(block: 'post');

        static::assertSame(
            self::buttons([ShareTarget::CopyLink], $options) . ShareScript::tag(),
            $helper(null, $options),
        );
    }

    #[Test]
    public function usesTheApplicationsDefaultsOverTheSitesLabels(): void
    {
        $helper = new Share(
            new ShareConfig([ShareTarget::X], labels: true),
            self::source(),
            defaults: new Options(
                icons: false,
                labels: false,
                newTab: false,
                block: 'post',
                class: 'post__share',
            ),
        );

        static::assertSame(
            self::buttons(
                [ShareTarget::X],
                new Options(
                    icons: false,
                    labels: false,
                    newTab: false,
                    block: 'post',
                    class: 'post__share',
                ),
            ),
            $helper(),
        );
    }

    #[Test]
    public function writesNothingWithoutButtons(): void
    {
        $helper = new Share(new ShareConfig([]), self::source());

        static::assertSame('', $helper());
    }

    #[Test]
    public function writesThePagesButtonsWithoutAScriptWhenNoneIsNeeded(): void
    {
        $helper = new Share(new ShareConfig([ShareTarget::X], labels: false), self::source());

        static::assertSame(self::buttons([ShareTarget::X], new Options(labels: false)), $helper());
    }

    #[Test]
    public function writesTheScriptAfterTheFirstButtonsThatNeedIt(): void
    {
        $helper = new Share(new ShareConfig([ShareTarget::CopyLink]), self::source(), new FixedNonce('n0nce'));

        static::assertSame(
            [
                self::buttons([ShareTarget::CopyLink], new Options(labels: true)) . ShareScript::tag('n0nce'),
                self::buttons([ShareTarget::CopyLink], new Options(labels: true)),
            ],
            [$helper(), $helper()],
        );
    }

    #[Test]
    public function writesTheScriptOnlyOnce(): void
    {
        $helper = new Share(new ShareConfig(), self::source());

        static::assertSame([ShareScript::tag('a'), ''], [$helper->script('a'), $helper->script('a')]);
    }

    #[Test]
    public function writesTheScriptWithoutANonceWhenThereIsNone(): void
    {
        $helper = new Share(new ShareConfig([ShareTarget::Share]), self::source());

        static::assertStringEndsWith(ShareScript::tag(), $helper(null, ['nonce' => 12]));
    }

    #[Test]
    public function writesTheScriptWithTheNonceItIsGiven(): void
    {
        $helper = new Share(new ShareConfig([ShareTarget::Share]), self::source(), new FixedNonce('n0nce'));

        static::assertStringEndsWith(ShareScript::tag('given'), $helper(null, ['nonce' => 'given']));
    }
}
