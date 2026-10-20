<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc;

/**
 * The CSP nonce for the page being rendered. Register one in the service
 * manager under this interface's name, and share() writes it on its script
 * without being told it.
 *
 * @api
 */
interface NonceProviderInterface
{
    /**
     * The nonce, or null when the page has none.
     */
    public function nonce(): ?string;
}
