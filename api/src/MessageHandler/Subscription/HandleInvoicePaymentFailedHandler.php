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

        // Only mark as past_due if subscription is currently active or trialing
        // Ignore payment_failed for already canceled/expired subscriptions
        if (!in_array($subscription->status, [Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIALING], true)) {
            $this->logger->info('Ignoring payment_failed for non-active subscription', [
                'subscriptionId' => $message->subscriptionId,
                'currentStatus' => $subscription->status,
            ]);
            return;
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

        $this->logger->warning('Subscription marked as past_due due to payment failure', [
            'subscriptionId' => $message->subscriptionId,
            'customerId' => $message->customerId,
        ]);
    }
}
