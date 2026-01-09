<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Subscription;

use FantasyAcademy\API\Entity\Subscription;
use FantasyAcademy\API\Repository\SubscriptionRepository;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

readonly final class SubscriptionChecker
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private ClockInterface $clock,
    ) {
    }

    public function isActive(Uuid $userId): bool
    {
        $subscription = $this->subscriptionRepository->findActiveByUser($userId);

        if ($subscription === null) {
            return false;
        }

        return $subscription->isActive($this->clock->now());
    }

    public function getActiveSubscription(Uuid $userId): ?Subscription
    {
        $subscription = $this->subscriptionRepository->findActiveByUser($userId);

        if ($subscription === null) {
            return null;
        }

        if (!$subscription->isActive($this->clock->now())) {
            return null;
        }

        return $subscription;
    }
}
