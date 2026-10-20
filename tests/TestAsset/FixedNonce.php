<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Tests\TestAsset;

use Contenir\Social\Laminas\Mvc\NonceProviderInterface;
use Override;

/**
 * A page whose CSP nonce is known in advance.
 */
final readonly class FixedNonce implements NonceProviderInterface
{
    public function __construct(
        private ?string $nonce,
    ) {}

    #[Override]
    public function nonce(): ?string
    {
        return $this->nonce;
    }
}
