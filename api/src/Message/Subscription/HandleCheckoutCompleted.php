<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\Subscription;

readonly final class HandleCheckoutCompleted
{
    public function __construct(
        public string $sessionId,
        public string $customerId,
        public string $subscriptionId,
        public ?string $customerEmail,
    ) {
    }
}
