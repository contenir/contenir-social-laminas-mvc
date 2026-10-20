<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Tests\Integration;

use Contenir\Social\Laminas\Mvc\NonceProviderInterface;
use Contenir\Social\Laminas\Mvc\Tests\TestAsset\FixedNonce;
use Contenir\Social\Laminas\Mvc\Tests\TestAsset\Settings;
use Contenir\Social\Laminas\Mvc\Tests\Trait\RendersViewsTrait;
use Contenir\Social\Script\ShareScript;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function preg_match;
use function substr_count;

/**
 * A layout calling socialProfiles() and share() through laminas-view, with
 * the module's configuration and the site's settings as the CMS wrote them.
 */
#[Group('integration')]
final class LayoutTest extends TestCase
{
    use RendersViewsTrait;

    #[Test]
    public function sharesThePageFromItsOpenGraphTagsInTheSitesOrder(): void
    {
        $this->setUpViews(['settings' => Settings::site()]);

        static::assertSame(
            1,
            preg_match(
                '#<main><ul class="social social--share">'
                    . '<li class="social__item social__item--x"><a class="social__link" href="https://x\.com/intent/tweet\?'
                    . 'text=The%202026%20winners&amp;url=https%3A%2F%2Fwww\.example\.com%2Fwinners".*'
                    . '<li class="social__item social__item--copy" hidden>.*</ul><script>#s',
                $this->renderer->render('layout'),
            ),
        );
    }

    #[Test]
    public function usesTheApplicationsDefaultOptions(): void
    {
        $this->setUpViews([
            'settings'        => Settings::site(),
            'contenir_social' => ['options' => ['block' => 'site', 'labels' => true]],
        ]);

        $html = $this->renderer->render('layout');

        static::assertSame(
            [1, 3, 0],
            [
                substr_count($html, needle: '<ul class="site site--profiles">'),
                substr_count($html, needle: '<span class="site__label">X</span>'),
                substr_count($html, needle: 'class="social'),
            ],
        );
    }

    #[Test]
    public function writesTheProfilesTheCoreHeldAsAddresses(): void
    {
        $this->setUpViews(['settings' => Settings::site()]);

        static::assertSame(
            1,
            preg_match(
                '#<header><ul class="social social--profiles">'
                    . '<li class="social__item social__item--facebook"><a class="social__link" '
                    . 'href="https://www.facebook.com/housesmagazine" rel="me noopener" target="_blank".*'
                    . 'href="https://www.instagram.com/housesmagazine/".*href="https://x.com/HousesMag".*</ul></header>#s',
                $this->renderer->render('layout'),
            ),
        );
    }

    #[Test]
    public function writesTheScriptOnceWithTheApplicationsNonce(): void
    {
        $this->setUpViews(
            ['settings' => Settings::site()],
            [NonceProviderInterface::class => new FixedNonce('page-nonce')],
        );

        $html = $this->renderer->render('layout');

        static::assertSame(
            [1, 2],
            [substr_count($html, ShareScript::tag('page-nonce')), substr_count($html, needle: 'social--share')],
        );
    }
}
