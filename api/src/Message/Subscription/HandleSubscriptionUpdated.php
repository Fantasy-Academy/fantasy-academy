<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\Subscription;

readonly final class HandleSubscriptionUpdated
{
    public function __construct(
        public string $subscriptionId,
        public string $customerId,
        public string $status,
        public int $currentPeriodStart,
        public int $currentPeriodEnd,
        public ?int $canceledAt,
        public bool $cancelAtPeriodEnd,
    ) {
    }
}
