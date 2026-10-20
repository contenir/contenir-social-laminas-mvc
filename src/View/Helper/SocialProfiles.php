<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\View\Helper;

use Contenir\Social\Html\Options;
use Contenir\Social\Html\ProfileLinks;
use Contenir\Social\Laminas\Mvc\HtmlOptions;
use Contenir\Social\Profiles;

/**
 * `$this->socialProfiles()` in a template: the site's profiles as escaped
 * links with rel="me", icons and accessible names.
 *
 *     <?= $this->socialProfiles() ?>
 *     <?= $this->socialProfiles(['block' => 'footer', 'labels' => true]) ?>
 *
 * @api
 */
final readonly class SocialProfiles
{
    public function __construct(
        private Profiles $profiles,
        private Options $defaults = new Options(),
    ) {}

    /**
     * The profiles themselves, for templates that write their own markup.
     */
    public function profiles(): Profiles
    {
        return $this->profiles;
    }

    /**
     * The list of links, or an empty string when the site has no profiles.
     *
     * @param Options|array<string, mixed> $options icons, labels, new_tab, block and class
     */
    public function __invoke(Options|array $options = []): string
    {
        return ProfileLinks::render($this->profiles, HtmlOptions::from($options, $this->defaults));
    }
}
