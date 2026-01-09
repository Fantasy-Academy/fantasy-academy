<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Stripe\Value;

readonly final class CheckoutSessionResult
{
    public function __construct(
        public string $sessionId,
        public string $url,
    ) {
    }
}
