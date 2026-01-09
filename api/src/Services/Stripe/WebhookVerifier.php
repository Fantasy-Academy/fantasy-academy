<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Stripe;

use FantasyAcademy\API\Exceptions\Stripe\InvalidWebhookSignature;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly final class WebhookVerifier implements WebhookVerifierInterface
{
    public function __construct(
        #[Autowire(env: 'STRIPE_WEBHOOK_SECRET')]
        private string $webhookSecret,
    ) {
    }

    /**
     * @throws InvalidWebhookSignature
     */
    public function verify(string $payload, string $signature): Event
    {
        try {
            return Webhook::constructEvent(
                $payload,
                $signature,
                $this->webhookSecret,
            );
        } catch (SignatureVerificationException $e) {
            throw new InvalidWebhookSignature($e->getMessage(), previous: $e);
        }
    }
}
