<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Stripe;

use FantasyAcademy\API\Services\Stripe\Value\CheckoutSessionResult;
use FantasyAcademy\API\Services\Stripe\Value\CustomerResult;
use FantasyAcademy\API\Services\Stripe\Value\PortalSessionResult;
use FantasyAcademy\API\Services\Stripe\Value\PriceResult;
use FantasyAcademy\API\Services\Stripe\Value\SubscriptionResult;

interface StripeClientInterface
{
    public function createCustomer(string $email, ?string $name = null, ?string $userId = null): CustomerResult;

    public function createCheckoutSession(
        string $customerId,
        string $priceId,
        string $successUrl,
        string $cancelUrl,
        ?string $userId = null,
    ): CheckoutSessionResult;

    public function createPortalSession(string $customerId, string $returnUrl): PortalSessionResult;

    /**
     * @return array<PriceResult>
     */
    public function getPricesByLookupKeys(string ...$lookupKeys): array;

    public function createProduct(string $name, string $appMetadataValue): string;

    public function createPrice(
        string $productId,
        int $unitAmount,
        string $currency,
        string $interval,
        string $lookupKey,
    ): PriceResult;

    public function findProductByMetadata(string $appMetadataValue): ?string;

    public function getSubscription(string $subscriptionId): SubscriptionResult;
}
