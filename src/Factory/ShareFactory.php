<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Factory;

use Contenir\Social\Laminas\Mvc\NonceProviderInterface;
use Contenir\Social\Laminas\Mvc\View\HeadMetadata;
use Contenir\Social\Laminas\Mvc\View\Helper\Share;
use Contenir\Social\Laminas\Mvc\View\PageSource;
use Contenir\Social\Share\ShareConfig;
use Laminas\Http\Request;
use Laminas\View\Helper\HeadLink;
use Laminas\View\Helper\HeadMeta;
use Laminas\View\Helper\HeadTitle;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * Builds share() from the site's share settings, as the CMS writes them,
 * and the page's metadata, read from the head helpers of the
 * "ViewHelperManager" (which contenir-resource's resourceMeta() and a site's
 * templates fill) and the "Request" service.
 *
 * @api
 */
final class ShareFactory
{
    public const string HELPERS = 'ViewHelperManager';

    public const string REQUEST = 'Request';

    /**
     * @throws ContainerExceptionInterface
     */
    private static function headLink(?ContainerInterface $helpers): HeadLink
    {
        $helper = true === $helpers?->has(HeadLink::class) ? $helpers->get(HeadLink::class) : null;

        return $helper instanceof HeadLink ? $helper : new HeadLink();
    }

    /**
     * @throws ContainerExceptionInterface
     */
    private static function headMeta(?ContainerInterface $helpers): HeadMeta
    {
        $helper = true === $helpers?->has(HeadMeta::class) ? $helpers->get(HeadMeta::class) : null;

        return $helper instanceof HeadMeta ? $helper : new HeadMeta();
    }

    /**
     * @throws ContainerExceptionInterface
     */
    private static function headTitle(?ContainerInterface $helpers): HeadTitle
    {
        $helper = true === $helpers?->has(HeadTitle::class) ? $helpers->get(HeadTitle::class) : null;

        return $helper instanceof HeadTitle ? $helper : new HeadTitle();
    }

    /**
     * @throws ContainerExceptionInterface
     *
     * @mago-expect analysis:mixed-assignment Services are untyped; each is checked here.
     */
    public function __invoke(ContainerInterface $container): Share
    {
        $config  = ConfigReader::from($container);
        $helpers = $container->get(self::HELPERS);
        $helpers = $helpers instanceof ContainerInterface ? $helpers : null;
        $request = $container->has(self::REQUEST) ? $container->get(self::REQUEST) : null;
        $nonces  = $container->has(NonceProviderInterface::class)
            ? $container->get(NonceProviderInterface::class)
            : null;

        return new Share(
            ShareConfig::fromSettings($config->settings()),
            new PageSource(
                new HeadMetadata(self::headMeta($helpers), self::headLink($helpers), self::headTitle($helpers)),
                $request instanceof Request ? $request->getUriString() : null,
            ),
            $nonces instanceof NonceProviderInterface ? $nonces : null,
            $config->options(),
        );
    }
}
