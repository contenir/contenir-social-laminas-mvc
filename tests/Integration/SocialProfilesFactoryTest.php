<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Tests\Integration;

use Contenir\Social\Laminas\Mvc\Factory\SocialProfilesFactory;
use Contenir\Social\Laminas\Mvc\Tests\TestAsset\ArrayContainer;
use Contenir\Social\Laminas\Mvc\Tests\TestAsset\Settings;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Building socialProfiles() from an application's config.
 */
#[Group('integration')]
final class SocialProfilesFactoryTest extends TestCase
{
    #[Test]
    public function readsTheProfilesFromTheSitesSettings(): void
    {
        $helper = (new SocialProfilesFactory())(new ArrayContainer(['config' => ['settings' => Settings::site()]]));

        static::assertSame(
            [
                'https://www.facebook.com/housesmagazine',
                'https://www.instagram.com/housesmagazine/',
                'https://x.com/HousesMag',
            ],
            $helper->profiles()->sameAs(),
        );
    }

    #[Test]
    public function writesWithTheApplicationsDefaultOptions(): void
    {
        $helper = (new SocialProfilesFactory())(new ArrayContainer([
            'config' => [
                'settings'        => Settings::site(),
                'contenir_social' => ['options' => ['block' => 'footer']],
            ],
        ]));

        static::assertStringStartsWith('<ul class="footer footer--profiles">', $helper());
    }
}
