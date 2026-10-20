<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Tests\Unit;

use Contenir\Social\Laminas\Mvc\ConfigProvider;
use Contenir\Social\Laminas\Mvc\Factory\ShareFactory;
use Contenir\Social\Laminas\Mvc\Factory\SocialProfilesFactory;
use Contenir\Social\Laminas\Mvc\Module;
use Contenir\Social\Laminas\Mvc\View\Helper\Share;
use Contenir\Social\Laminas\Mvc\View\Helper\SocialProfiles;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class ConfigProviderTest extends TestCase
{
    #[Test]
    public function givesTheModuleTheSameConfig(): void
    {
        static::assertSame((new ConfigProvider())(), (new Module())->getConfig());
    }

    #[Test]
    public function registersBothHelpersUnderTheirNames(): void
    {
        static::assertSame(
            [
                'view_helpers' => [
                    'aliases'   => [
                        'socialProfiles' => SocialProfiles::class,
                        'SocialProfiles' => SocialProfiles::class,
                        'share'          => Share::class,
                        'Share'          => Share::class,
                    ],
                    'factories' => [
                        SocialProfiles::class => SocialProfilesFactory::class,
                        Share::class          => ShareFactory::class,
                    ],
                ],
            ],
            (new ConfigProvider())(),
        );
    }
}
