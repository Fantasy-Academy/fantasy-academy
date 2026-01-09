<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Stripe;

use FantasyAcademy\API\Exceptions\Stripe\InvalidWebhookSignature;
use Stripe\Event;

interface WebhookVerifierInterface
{
    /**
     * @throws InvalidWebhookSignature
     */
    public function verify(string $payload, string $signature): Event;
}
