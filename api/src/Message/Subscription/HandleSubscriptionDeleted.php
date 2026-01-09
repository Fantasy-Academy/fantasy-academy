<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\Subscription;

readonly final class HandleSubscriptionDeleted
{
    public function __construct(
        public string $subscriptionId,
    ) {
    }
}
