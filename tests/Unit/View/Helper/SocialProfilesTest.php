<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Tests\Unit\View\Helper;

use Contenir\Social\Html\Options;
use Contenir\Social\Html\ProfileLinks;
use Contenir\Social\Laminas\Mvc\View\Helper\SocialProfiles;
use Contenir\Social\Network;
use Contenir\Social\Profiles;
use Contenir\Social\ProfileUrl;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class SocialProfilesTest extends TestCase
{
    private static function profiles(): Profiles
    {
        return new Profiles([ProfileUrl::parse(Network::Instagram, '@houses')]);
    }

    #[Test]
    public function givesTheProfilesToTemplatesThatWriteTheirOwn(): void
    {
        $profiles = self::profiles();

        static::assertSame($profiles, (new SocialProfiles($profiles))->profiles());
    }

    #[Test]
    public function writesInTheSocialBlockWithoutDefaults(): void
    {
        static::assertStringStartsWith(
            '<ul class="social social--profiles">',
            (new SocialProfiles(self::profiles()))(),
        );
    }

    #[Test]
    public function writesTheProfilesWithTheDefaults(): void
    {
        $helper = new SocialProfiles(self::profiles(), new Options(block: 'footer'));

        static::assertSame(
            ProfileLinks::render(self::profiles(), new Options(block: 'footer')),
            $helper(),
        );
    }

    #[Test]
    public function writesTheProfilesWithTheTemplatesOptions(): void
    {
        $helper = new SocialProfiles(self::profiles(), new Options(block: 'footer'));

        static::assertSame(
            ProfileLinks::render(self::profiles(), new Options(
                labels: true,
                block: 'footer',
            )),
            $helper(['labels' => true]),
        );
    }
}
