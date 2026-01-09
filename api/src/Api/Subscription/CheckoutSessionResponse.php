<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Subscription;

final class CheckoutSessionResponse
{
    public function __construct(
        public string $checkoutUrl,
        public string $sessionId,
    ) {
    }
}
