<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Tests\TestAsset;

/**
 * Site settings as the CMS writes them to settings.global.php.
 */
final class Settings
{
    /**
     * Profiles as the core Social Media Links plugin left them, and share
     * buttons chosen in the add-on.
     *
     * @return array<string, mixed>
     */
    public static function site(): array
    {
        return [
            'social'     => [
                'social_facebook'  => 'https://www.facebook.com/housesmagazine',
                'social_twitter'   => '@HousesMag',
                'social_instagram' => 'housesmagazine',
                'social_linkedin'  => null,
            ],
            'extensions' => [
                'social' => [
                    'share_networks' => [
                        ['fields' => ['network' => 'x']],
                        ['fields' => ['network' => 'copy']],
                    ],
                    'share_labels'   => '0',
                    'share_utm'      => '0',
                ],
            ],
        ];
    }
}
