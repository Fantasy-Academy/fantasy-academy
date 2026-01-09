<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Stripe;

use DateTimeImmutable;
use FantasyAcademy\API\Services\Stripe\Value\CheckoutSessionResult;
use FantasyAcademy\API\Services\Stripe\Value\CustomerResult;
use FantasyAcademy\API\Services\Stripe\Value\PortalSessionResult;
use FantasyAcademy\API\Services\Stripe\Value\PriceResult;
use FantasyAcademy\API\Services\Stripe\Value\SubscriptionResult;
use Stripe\StripeClient as BaseStripeClient;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly final class StripeClient implements StripeClientInterface
{
    private BaseStripeClient $client;

    public function __construct(
        #[Autowire(env: 'STRIPE_SECRET_KEY')]
        string $secretKey,
    ) {
        $this->client = new BaseStripeClient($secretKey);
    }

    public function createCustomer(string $email, ?string $name = null, ?string $userId = null): CustomerResult
    {
        $params = ['email' => $email];

        if ($name !== null) {
            $params['name'] = $name;
        }

        if ($userId !== null) {
            $params['metadata'] = ['user_id' => $userId];
        }

        $customer = $this->client->customers->create($params);

        return new CustomerResult(
            customerId: $customer->id,
            email: $customer->email ?? $email,
        );
    }

    public function createCheckoutSession(
        string $customerId,
        string $priceId,
        string $successUrl,
        string $cancelUrl,
        ?string $userId = null,
    ): CheckoutSessionResult {
        $params = [
            'customer' => $customerId,
            'mode' => 'subscription',
            'line_items' => [
                [
                    'price' => $priceId,
                    'quantity' => 1,
                ],
            ],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ];

        if ($userId !== null) {
            $params['metadata'] = ['user_id' => $userId];
            $params['subscription_data'] = ['metadata' => ['user_id' => $userId]];
        }

        $session = $this->client->checkout->sessions->create($params);

        return new CheckoutSessionResult(
            sessionId: $session->id,
            url: $session->url ?? '',
        );
    }

    public function createPortalSession(string $customerId, string $returnUrl): PortalSessionResult
    {
        $session = $this->client->billingPortal->sessions->create([
            'customer' => $customerId,
            'return_url' => $returnUrl,
        ]);

        return new PortalSessionResult(
            url: $session->url,
        );
    }

    /**
     * @return array<PriceResult>
     */
    public function getPricesByLookupKeys(string ...$lookupKeys): array
    {
        $prices = $this->client->prices->all([
            'lookup_keys' => $lookupKeys,
            'active' => true,
            'expand' => ['data.product'],
        ]);

        $results = [];
        foreach ($prices->data as $price) {
            $results[] = new PriceResult(
                priceId: $price->id,
                productId: is_string($price->product) ? $price->product : $price->product->id,
                unitAmount: $price->unit_amount ?? 0,
                currency: $price->currency,
                interval: $price->recurring !== null ? $price->recurring->interval : 'month',
                lookupKey: $price->lookup_key ?? '',
            );
        }

        return $results;
    }

    public function createProduct(string $name, string $appMetadataValue): string
    {
        $product = $this->client->products->create([
            'name' => $name,
            'metadata' => [
                'app' => $appMetadataValue,
            ],
        ]);

        return $product->id;
    }

    public function createPrice(
        string $productId,
        int $unitAmount,
        string $currency,
        string $interval,
        string $lookupKey,
    ): PriceResult {
        $price = $this->client->prices->create([
            'product' => $productId,
            'unit_amount' => $unitAmount,
            'currency' => $currency,
            'recurring' => [
                'interval' => $interval,
            ],
            'lookup_key' => $lookupKey,
            'transfer_lookup_key' => true,
        ]);

        return new PriceResult(
            priceId: $price->id,
            productId: $productId,
            unitAmount: $price->unit_amount ?? 0,
            currency: $price->currency,
            interval: $price->recurring !== null ? $price->recurring->interval : $interval,
            lookupKey: $price->lookup_key ?? $lookupKey,
        );
    }

    public function findProductByMetadata(string $appMetadataValue): ?string
    {
        $products = $this->client->products->all([
            'active' => true,
            'limit' => 100,
        ]);

        foreach ($products->data as $product) {
            if (isset($product->metadata['app']) && $product->metadata['app'] === $appMetadataValue) {
                return $product->id;
            }
        }

        return null;
    }

    public function getSubscription(string $subscriptionId): SubscriptionResult
    {
        $subscription = $this->client->subscriptions->retrieve($subscriptionId, [
            'expand' => ['items.data.price'],
        ]);

        /** @var array<string, mixed> $subArray */
        $subArray = $subscription->toArray();

        $priceId = null;
        $items = $subArray['items'] ?? null;
        if (is_array($items)) {
            $data = $items['data'] ?? null;
            if (is_array($data) && isset($data[0]) && is_array($data[0])) {
                $price = $data[0]['price'] ?? null;
                if (is_array($price) && is_string($price['id'] ?? null)) {
                    $priceId = $price['id'];
                }
            }
        }

        $customerId = is_string($subArray['customer'] ?? null)
            ? $subArray['customer']
            : '';

        $currentPeriodStart = is_int($subArray['current_period_start'] ?? null)
            ? $subArray['current_period_start']
            : 0;
        $currentPeriodEnd = is_int($subArray['current_period_end'] ?? null)
            ? $subArray['current_period_end']
            : 0;
        $canceledAt = is_int($subArray['canceled_at'] ?? null)
            ? $subArray['canceled_at']
            : null;

        return new SubscriptionResult(
            subscriptionId: $subscription->id,
            customerId: $customerId,
            status: $subscription->status,
            priceId: $priceId,
            currentPeriodStart: (new DateTimeImmutable())->setTimestamp($currentPeriodStart),
            currentPeriodEnd: (new DateTimeImmutable())->setTimestamp($currentPeriodEnd),
            canceledAt: $canceledAt !== null
                ? (new DateTimeImmutable())->setTimestamp($canceledAt)
                : null,
            cancelAtPeriodEnd: (bool) ($subArray['cancel_at_period_end'] ?? false),
        );
    }
}
