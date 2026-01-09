<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api\Subscription;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Services\Stripe\StripeClientInterface;
use FantasyAcademy\API\Services\Stripe\Value\SubscriptionResult;
use FantasyAcademy\API\Services\Stripe\WebhookVerifierInterface;
use FantasyAcademy\API\Tests\DataFixtures\SubscriptionFixture;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;
use Stripe\Event;

/**
 * @covers \FantasyAcademy\API\Controller\StripeWebhookController
 * @covers \FantasyAcademy\API\MessageHandler\Subscription\HandleCheckoutCompletedHandler
 * @covers \FantasyAcademy\API\MessageHandler\Subscription\HandleSubscriptionUpdatedHandler
 * @covers \FantasyAcademy\API\MessageHandler\Subscription\HandleSubscriptionDeletedHandler
 * @covers \FantasyAcademy\API\MessageHandler\Subscription\HandleInvoicePaymentFailedHandler
 */
final class StripeWebhookTest extends ApiTestCase
{
    public function testRejectsInvalidSignature(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/webhooks/stripe', [
            'headers' => [
                'Stripe-Signature' => 'invalid_signature',
            ],
            'body' => '{}',
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains(['error' => 'Invalid signature']);
    }

    public function testHandlesCheckoutSessionCompleted(): void
    {
        $client = self::createClient();
        $container = $client->getContainer();

        // Create a mock event for checkout.session.completed
        $eventData = [
            'id' => 'evt_test_checkout',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_session',
                    'customer' => SubscriptionFixture::ACTIVE_STRIPE_CUSTOMER_ID,
                    'subscription' => 'sub_new_test_123',
                    'customer_email' => 'user@example.com',
                ],
            ],
        ];

        // Stub the webhook verifier
        $verifierStub = $this->createStub(WebhookVerifierInterface::class);
        $verifierStub
            ->method('verify')
            ->willReturn(Event::constructFrom($eventData));
        $container->set(WebhookVerifierInterface::class, $verifierStub);

        // Stub the Stripe client to return subscription details
        $stripeClientStub = $this->createStub(StripeClientInterface::class);
        $stripeClientStub
            ->method('getSubscription')
            ->willReturn(new SubscriptionResult(
                subscriptionId: 'sub_new_test_123',
                customerId: SubscriptionFixture::ACTIVE_STRIPE_CUSTOMER_ID,
                status: 'active',
                priceId: 'price_monthly_test',
                currentPeriodStart: new \DateTimeImmutable(),
                currentPeriodEnd: new \DateTimeImmutable('+30 days'),
                canceledAt: null,
                cancelAtPeriodEnd: false,
            ));
        $container->set(StripeClientInterface::class, $stripeClientStub);

        $client->request('POST', '/api/webhooks/stripe', [
            'headers' => [
                'Stripe-Signature' => 'valid_signature',
            ],
            'body' => json_encode($eventData),
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['received' => true]);
    }

    public function testHandlesSubscriptionUpdated(): void
    {
        $client = self::createClient();
        $container = $client->getContainer();

        $now = time();

        // Create a mock event for customer.subscription.updated
        $eventData = [
            'id' => 'evt_test_update',
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'id' => SubscriptionFixture::ACTIVE_STRIPE_SUBSCRIPTION_ID,
                    'customer' => SubscriptionFixture::ACTIVE_STRIPE_CUSTOMER_ID,
                    'status' => 'active',
                    'current_period_start' => $now,
                    'current_period_end' => $now + 86400 * 30,
                    'canceled_at' => null,
                    'cancel_at_period_end' => true,
                ],
            ],
        ];

        // Stub the webhook verifier
        $verifierStub = $this->createStub(WebhookVerifierInterface::class);
        $verifierStub
            ->method('verify')
            ->willReturn(Event::constructFrom($eventData));
        $container->set(WebhookVerifierInterface::class, $verifierStub);

        $client->request('POST', '/api/webhooks/stripe', [
            'headers' => [
                'Stripe-Signature' => 'valid_signature',
            ],
            'body' => json_encode($eventData),
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['received' => true]);
    }

    public function testHandlesSubscriptionDeleted(): void
    {
        $client = self::createClient();
        $container = $client->getContainer();

        // Create a mock event for customer.subscription.deleted
        $eventData = [
            'id' => 'evt_test_delete',
            'type' => 'customer.subscription.deleted',
            'data' => [
                'object' => [
                    'id' => SubscriptionFixture::ACTIVE_STRIPE_SUBSCRIPTION_ID,
                    'customer' => SubscriptionFixture::ACTIVE_STRIPE_CUSTOMER_ID,
                    'status' => 'canceled',
                ],
            ],
        ];

        // Stub the webhook verifier
        $verifierStub = $this->createStub(WebhookVerifierInterface::class);
        $verifierStub
            ->method('verify')
            ->willReturn(Event::constructFrom($eventData));
        $container->set(WebhookVerifierInterface::class, $verifierStub);

        $client->request('POST', '/api/webhooks/stripe', [
            'headers' => [
                'Stripe-Signature' => 'valid_signature',
            ],
            'body' => json_encode($eventData),
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['received' => true]);
    }

    public function testHandlesInvoicePaymentFailed(): void
    {
        $client = self::createClient();
        $container = $client->getContainer();

        $eventData = [
            'id' => 'evt_test_payment_failed',
            'type' => 'invoice.payment_failed',
            'data' => [
                'object' => [
                    'id' => 'in_test_invoice',
                    'subscription' => SubscriptionFixture::ACTIVE_STRIPE_SUBSCRIPTION_ID,
                    'customer' => SubscriptionFixture::ACTIVE_STRIPE_CUSTOMER_ID,
                ],
            ],
        ];

        $verifierStub = $this->createStub(WebhookVerifierInterface::class);
        $verifierStub
            ->method('verify')
            ->willReturn(Event::constructFrom($eventData));
        $container->set(WebhookVerifierInterface::class, $verifierStub);

        $client->request('POST', '/api/webhooks/stripe', [
            'headers' => [
                'Stripe-Signature' => 'valid_signature',
            ],
            'body' => json_encode($eventData),
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['received' => true]);
    }

    public function testHandlesUnknownEventTypeGracefully(): void
    {
        $client = self::createClient();
        $container = $client->getContainer();

        // Create a mock event for unknown type
        $eventData = [
            'id' => 'evt_test_unknown',
            'type' => 'unknown.event.type',
            'data' => [
                'object' => [
                    'id' => 'obj_test',
                ],
            ],
        ];

        // Stub the webhook verifier
        $verifierStub = $this->createStub(WebhookVerifierInterface::class);
        $verifierStub
            ->method('verify')
            ->willReturn(Event::constructFrom($eventData));
        $container->set(WebhookVerifierInterface::class, $verifierStub);

        $client->request('POST', '/api/webhooks/stripe', [
            'headers' => [
                'Stripe-Signature' => 'valid_signature',
            ],
            'body' => json_encode($eventData),
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['received' => true]);
    }
}
