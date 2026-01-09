<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\Subscription;

use DateTimeImmutable;
use FantasyAcademy\API\Message\Subscription\HandleSubscriptionUpdated;
use FantasyAcademy\API\Repository\SubscriptionRepository;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly final class HandleSubscriptionUpdatedHandler
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private ClockInterface $clock,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(HandleSubscriptionUpdated $message): void
    {
        $subscription = $this->subscriptionRepository->findByStripeSubscriptionId($message->subscriptionId);

        if ($subscription === null) {
            $this->logger->warning('Subscription not found for update', [
                'subscriptionId' => $message->subscriptionId,
            ]);
            return;
        }

        $now = $this->clock->now();

        $subscription->updateFromStripe(
            status: $message->status,
            currentPeriodStart: (new DateTimeImmutable())->setTimestamp($message->currentPeriodStart),
            currentPeriodEnd: (new DateTimeImmutable())->setTimestamp($message->currentPeriodEnd),
            canceledAt: $message->canceledAt !== null
                ? (new DateTimeImmutable())->setTimestamp($message->canceledAt)
                : null,
            cancelAtPeriodEnd: $message->cancelAtPeriodEnd,
            now: $now,
        );

        $this->logger->info('Subscription updated from webhook', [
            'subscriptionId' => $message->subscriptionId,
            'status' => $message->status,
        ]);
    }
}
