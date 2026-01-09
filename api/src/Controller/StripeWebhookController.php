<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Controller;

use FantasyAcademy\API\Exceptions\Stripe\InvalidWebhookSignature;
use FantasyAcademy\API\Message\Subscription\HandleCheckoutCompleted;
use FantasyAcademy\API\Message\Subscription\HandleSubscriptionDeleted;
use FantasyAcademy\API\Message\Subscription\HandleSubscriptionUpdated;
use FantasyAcademy\API\Services\Stripe\WebhookVerifierInterface;
use Stripe\Checkout\Session;
use Stripe\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class StripeWebhookController extends AbstractController
{
    public function __construct(
        private readonly WebhookVerifierInterface $webhookVerifier,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    #[Route(path: '/api/webhooks/stripe', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->headers->get('Stripe-Signature', '');

        try {
            $event = $this->webhookVerifier->verify($payload, $signature);
        } catch (InvalidWebhookSignature) {
            return $this->json(['error' => 'Invalid signature'], Response::HTTP_BAD_REQUEST);
        }

        $data = $event->data->object;

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($data),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($data),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($data),
            default => null,
        };

        return $this->json(['received' => true]);
    }

    /**
     * @param Session|\Stripe\StripeObject $session
     */
    private function handleCheckoutCompleted($session): void
    {
        /** @var array<string, mixed> $sessionArray */
        $sessionArray = $session->toArray();

        $sessionId = is_string($session->id) ? $session->id : '';
        $customerId = is_string($sessionArray['customer'] ?? null) ? $sessionArray['customer'] : null;
        $subscriptionId = is_string($sessionArray['subscription'] ?? null) ? $sessionArray['subscription'] : null;

        $customerEmail = null;
        if (is_string($sessionArray['customer_email'] ?? null)) {
            $customerEmail = $sessionArray['customer_email'];
        } elseif (is_array($sessionArray['customer_details'] ?? null)) {
            $details = $sessionArray['customer_details'];
            if (is_string($details['email'] ?? null)) {
                $customerEmail = $details['email'];
            }
        }

        if ($customerId === null || $subscriptionId === null || $sessionId === '') {
            return;
        }

        $this->messageBus->dispatch(new HandleCheckoutCompleted(
            sessionId: $sessionId,
            customerId: $customerId,
            subscriptionId: $subscriptionId,
            customerEmail: $customerEmail,
        ));
    }

    /**
     * @param Subscription|\Stripe\StripeObject $subscription
     */
    private function handleSubscriptionUpdated($subscription): void
    {
        /** @var array<string, mixed> $subArray */
        $subArray = $subscription->toArray();

        $subscriptionId = is_string($subscription->id) ? $subscription->id : null;
        $customerId = is_string($subArray['customer'] ?? null) ? $subArray['customer'] : null;

        if ($customerId === null || $subscriptionId === null) {
            return;
        }

        $this->messageBus->dispatch(new HandleSubscriptionUpdated(
            subscriptionId: $subscriptionId,
            customerId: $customerId,
            status: is_string($subArray['status'] ?? null) ? $subArray['status'] : 'unknown',
            currentPeriodStart: is_int($subArray['current_period_start'] ?? null) ? $subArray['current_period_start'] : 0,
            currentPeriodEnd: is_int($subArray['current_period_end'] ?? null) ? $subArray['current_period_end'] : 0,
            canceledAt: is_int($subArray['canceled_at'] ?? null) ? $subArray['canceled_at'] : null,
            cancelAtPeriodEnd: (bool) ($subArray['cancel_at_period_end'] ?? false),
        ));
    }

    /**
     * @param Subscription|\Stripe\StripeObject $subscription
     */
    private function handleSubscriptionDeleted($subscription): void
    {
        $subscriptionId = is_string($subscription->id) ? $subscription->id : null;

        if ($subscriptionId === null) {
            return;
        }

        $this->messageBus->dispatch(new HandleSubscriptionDeleted(
            subscriptionId: $subscriptionId,
        ));
    }
}
