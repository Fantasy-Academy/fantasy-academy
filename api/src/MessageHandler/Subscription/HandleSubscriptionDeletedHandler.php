<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\Subscription;

use FantasyAcademy\API\Entity\Subscription;
use FantasyAcademy\API\Message\Subscription\HandleSubscriptionDeleted;
use FantasyAcademy\API\Repository\SubscriptionRepository;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly final class HandleSubscriptionDeletedHandler
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private ClockInterface $clock,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(HandleSubscriptionDeleted $message): void
    {
        $subscription = $this->subscriptionRepository->findByStripeSubscriptionId($message->subscriptionId);

        if ($subscription === null) {
            $this->logger->warning('Subscription not found for deletion', [
                'subscriptionId' => $message->subscriptionId,
            ]);
            return;
        }

        $now = $this->clock->now();

        // Mark as canceled - we don't delete the record, just update status
        $subscription->updateFromStripe(
            status: Subscription::STATUS_CANCELED,
            currentPeriodStart: $subscription->currentPeriodStart,
            currentPeriodEnd: $subscription->currentPeriodEnd,
            canceledAt: $now,
            cancelAtPeriodEnd: false,
            now: $now,
        );

        $this->logger->info('Subscription marked as deleted', [
            'subscriptionId' => $message->subscriptionId,
        ]);
    }
}
