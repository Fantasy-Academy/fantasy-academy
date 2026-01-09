<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\Subscription;

use DateTimeImmutable;
use FantasyAcademy\API\Entity\Subscription;
use FantasyAcademy\API\Message\Subscription\HandleCheckoutCompleted;
use FantasyAcademy\API\Repository\SubscriptionRepository;
use FantasyAcademy\API\Repository\UserRepository;
use FantasyAcademy\API\Services\Stripe\StripeClientInterface;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
readonly final class HandleCheckoutCompletedHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private SubscriptionRepository $subscriptionRepository,
        private StripeClientInterface $stripeClient,
        private ClockInterface $clock,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(HandleCheckoutCompleted $message): void
    {
        // Check if subscription already exists (idempotency)
        $existingSubscription = $this->subscriptionRepository->findByStripeSubscriptionId($message->subscriptionId);
        if ($existingSubscription !== null) {
            $this->logger->info('Subscription already exists, skipping', [
                'subscriptionId' => $message->subscriptionId,
            ]);
            return;
        }

        // Find user by Stripe customer ID
        $user = $this->userRepository->findByStripeCustomerId($message->customerId);
        if ($user === null) {
            $this->logger->warning('User not found for Stripe customer ID', [
                'customerId' => $message->customerId,
                'sessionId' => $message->sessionId,
            ]);
            return;
        }

        // Get subscription details from Stripe
        $stripeSubscription = $this->stripeClient->getSubscription($message->subscriptionId);

        $now = $this->clock->now();

        $subscription = new Subscription(
            id: Uuid::v7(),
            user: $user,
            stripeSubscriptionId: $message->subscriptionId,
            stripeCustomerId: $message->customerId,
            planId: $stripeSubscription->priceId,
            currentPeriodStart: $stripeSubscription->currentPeriodStart,
            currentPeriodEnd: $stripeSubscription->currentPeriodEnd,
            status: $stripeSubscription->status,
            createdAt: $now,
        );

        $this->subscriptionRepository->add($subscription);

        $this->logger->info('Subscription created from checkout', [
            'subscriptionId' => $message->subscriptionId,
            'userId' => $user->id->toString(),
        ]);
    }
}
