<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc;

use Contenir\Social\Laminas\Mvc\Factory\ShareFactory;
use Contenir\Social\Laminas\Mvc\Factory\SocialProfilesFactory;
use Contenir\Social\Laminas\Mvc\View\Helper\Share;
use Contenir\Social\Laminas\Mvc\View\Helper\SocialProfiles;

/**
 * The view helpers the module registers: socialProfiles() and share().
 *
 * @api
 */
final class ConfigProvider
{
    /**
     * @return array<string, array<string, array<string, string>>>
     */
    public function __invoke(): array
    {
        return [
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
        ];
    }
}
