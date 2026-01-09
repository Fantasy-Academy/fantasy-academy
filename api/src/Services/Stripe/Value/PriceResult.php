<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Stripe\Value;

readonly final class PriceResult
{
    public function __construct(
        public string $priceId,
        public string $productId,
        public int $unitAmount,
        public string $currency,
        public string $interval,
        public string $lookupKey,
    ) {
    }
}
