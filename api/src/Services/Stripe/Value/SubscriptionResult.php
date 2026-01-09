<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Stripe\Value;

use DateTimeImmutable;

readonly final class SubscriptionResult
{
    public function __construct(
        public string $subscriptionId,
        public string $customerId,
        public string $status,
        public ?string $priceId,
        public DateTimeImmutable $currentPeriodStart,
        public DateTimeImmutable $currentPeriodEnd,
        public ?DateTimeImmutable $canceledAt,
        public bool $cancelAtPeriodEnd,
    ) {
    }
}
