<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Factory;

use Contenir\Social\Laminas\Mvc\View\Helper\SocialProfiles;
use Contenir\Social\Profiles;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * Builds socialProfiles() from the site's settings, as the CMS writes them.
 *
 * @api
 */
final class SocialProfilesFactory
{
    /**
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): SocialProfiles
    {
        $config = ConfigReader::from($container);

        return new SocialProfiles(Profiles::fromSettings($config->settings()), $config->options());
    }
}
