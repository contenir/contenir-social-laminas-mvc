<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Factory;

use Contenir\Social\Html\Options;
use Contenir\Social\Laminas\Mvc\HtmlOptions;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function is_array;

/**
 * The parts of the application's config the helpers read: the site's
 * settings (config['settings'], as the CMS writes them) and the default
 * HTML options (config['contenir_social']['options']).
 *
 * @internal
 */
final readonly class ConfigReader
{
    /**
     * @param array<array-key, mixed> $config
     */
    private function __construct(
        private array $config,
    ) {}

    /**
     * @throws ContainerExceptionInterface
     *
     * @mago-expect analysis:mixed-assignment The application's config is untyped; it is checked here.
     */
    public static function from(ContainerInterface $container): self
    {
        $config = $container->get('config');

        return new self(is_array($config) ? $config : []);
    }

    /**
     * @mago-expect analysis:mixed-assignment The application's config is untyped; each level is checked here.
     */
    public function options(): Options
    {
        $social  = $this->config['contenir_social'] ?? null;
        $options = is_array($social) ? $social['options'] ?? null : null;

        return HtmlOptions::from(is_array($options) ? $options : []);
    }

    /**
     * @return array<array-key, mixed>
     *
     * @mago-expect analysis:mixed-assignment The application's config is untyped; it is checked here.
     */
    public function settings(): array
    {
        $settings = $this->config['settings'] ?? null;

        return is_array($settings) ? $settings : [];
    }
}
