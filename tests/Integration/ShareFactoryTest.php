<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Tests\Integration;

use Contenir\Social\Laminas\Mvc\Factory\ShareFactory;
use Contenir\Social\Laminas\Mvc\NonceProviderInterface;
use Contenir\Social\Laminas\Mvc\Tests\TestAsset\ArrayContainer;
use Contenir\Social\Laminas\Mvc\Tests\TestAsset\FixedNonce;
use Contenir\Social\Script\ShareScript;
use Laminas\Http\Request;
use Laminas\View\Helper\HeadLink;
use Laminas\View\Helper\HeadMeta;
use Laminas\View\Helper\HeadTitle;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Building share() from an application's services, as laminas-view's
 * plugin manager does.
 */
#[Group('integration')]
final class ShareFactoryTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private static function config(string $buttons): array
    {
        return ['settings' => ['extensions' => ['social' => ['share_networks' => $buttons]]]];
    }

    #[Test]
    public function ignoresHelpersOfTheWrongKind(): void
    {
        $request = new Request();
        $request->setUri('https://www.example.com/winners');

        $share = (new ShareFactory())(new ArrayContainer([
            'config'            => self::config('copy'),
            'ViewHelperManager' => new ArrayContainer([
                HeadLink::class  => 'none',
                HeadMeta::class  => 'none',
                HeadTitle::class => 'none',
            ]),
            'Request'           => $request,
        ]));

        static::assertStringContainsString('data-social-copy="https://www.example.com/winners"', $share());
    }

    #[Test]
    public function readsThePageFromTheApplicationsHeadHelpers(): void
    {
        $links = new HeadLink();
        $links(['rel' => 'canonical', 'href' => 'https://www.example.com/canonical']);

        $share = (new ShareFactory())(new ArrayContainer([
            'config'            => self::config('copy'),
            'ViewHelperManager' => new ArrayContainer([
                HeadLink::class  => $links,
                HeadMeta::class  => new HeadMeta(),
                HeadTitle::class => new HeadTitle(),
            ]),
        ]));

        static::assertStringContainsString('data-social-copy="https://www.example.com/canonical"', $share());
    }

    #[Test]
    public function sharesTheCurrentAddressWhenThePageSaysNothing(): void
    {
        $request = new Request();
        $request->setUri('https://www.example.com/winners');

        $share = (new ShareFactory())(new ArrayContainer([
            'config'            => self::config('linkedin'),
            'ViewHelperManager' => new ArrayContainer([]),
            'Request'           => $request,
        ]));

        static::assertStringContainsString('share-offsite/?url=https%3A%2F%2Fwww.example.com%2Fwinners"', $share());
    }

    #[Test]
    public function writesTheScriptWithoutANonceWhenTheProviderIsOfTheWrongKind(): void
    {
        $share = (new ShareFactory())(new ArrayContainer([
            'config'                      => self::config('copy'),
            'ViewHelperManager'           => 'none',
            NonceProviderInterface::class => 'none',
        ]));

        static::assertSame(ShareScript::tag(), $share->script());
    }

    #[Test]
    public function writesTheScriptWithTheApplicationsNonce(): void
    {
        $share = (new ShareFactory())(new ArrayContainer([
            'config'                      => self::config('copy'),
            'ViewHelperManager'           => 'none',
            NonceProviderInterface::class => new FixedNonce('page-nonce'),
        ]));

        static::assertSame(ShareScript::tag('page-nonce'), $share->script());
    }
}
