<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc;

/**
 * Registers the socialProfiles() and share() view helpers with a Laminas
 * MVC application.
 *
 * @api
 */
final class Module
{
    /**
     * @return array<string, array<string, array<string, string>>>
     */
    public function getConfig(): array
    {
        return (new ConfigProvider())();
    }
}
