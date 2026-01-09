<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\Subscription;

use FantasyAcademy\API\Entity\Subscription;
use FantasyAcademy\API\Exceptions\SubscriptionNotFound;
use FantasyAcademy\API\Message\Subscription\HandleInvoicePaymentFailed;
use FantasyAcademy\API\Repository\SubscriptionRepository;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly final class HandleInvoicePaymentFailedHandler
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private ClockInterface $clock,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws SubscriptionNotFound
     */
    public function __invoke(HandleInvoicePaymentFailed $message): void
    {
        $subscription = $this->subscriptionRepository->findByStripeSubscriptionId($message->subscriptionId);

        if ($subscription === null) {
            throw new SubscriptionNotFound();
        }

        $now = $this->clock->now();

        $subscription->updateFromStripe(
            status: Subscription::STATUS_PAST_DUE,
            currentPeriodStart: $subscription->currentPeriodStart,
            currentPeriodEnd: $subscription->currentPeriodEnd,
            canceledAt: $subscription->canceledAt,
            cancelAtPeriodEnd: $subscription->cancelAtPeriodEnd,
            now: $now,
        );

        $this->logger->warning('Subscription payment failed', [
            'subscriptionId' => $message->subscriptionId,
            'customerId' => $message->customerId,
        ]);
    }
}
