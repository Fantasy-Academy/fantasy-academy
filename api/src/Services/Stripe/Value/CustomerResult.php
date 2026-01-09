<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Stripe\Value;

readonly final class CustomerResult
{
    public function __construct(
        public string $customerId,
        public string $email,
    ) {
    }
}
