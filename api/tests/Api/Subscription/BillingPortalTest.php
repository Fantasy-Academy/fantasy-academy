<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api\Subscription;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Services\Stripe\StripeClientInterface;
use FantasyAcademy\API\Services\Stripe\Value\PortalSessionResult;
use FantasyAcademy\API\Tests\DataFixtures\SubscriptionFixture;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;
use FantasyAcademy\API\Tests\TestingLogin;

/**
 * @covers \FantasyAcademy\API\Message\Subscription\CreatePortalSession
 * @covers \FantasyAcademy\API\MessageHandler\Subscription\CreatePortalSessionHandler
 */
final class BillingPortalTest extends ApiTestCase
{
    public function testRequiresAuthentication(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/subscription/portal', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [],
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreatesPortalSessionForSubscriber(): void
    {
        $client = self::createClient();
        $container = $client->getContainer();

        // Mock Stripe client
        $mockStripeClient = $this->createMock(StripeClientInterface::class);
        $mockStripeClient
            ->method('createPortalSession')
            ->with(SubscriptionFixture::ACTIVE_STRIPE_CUSTOMER_ID, $this->isType('string'))
            ->willReturn(new PortalSessionResult(
                url: 'https://billing.stripe.com/session/test_portal_session',
            ));
        $container->set(StripeClientInterface::class, $mockStripeClient);

        $jwt = TestingLogin::getJwt($client, UserFixture::USER_2_EMAIL);

        $client->request('POST', '/api/subscription/portal', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $jwt,
            ],
            'json' => [],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'portalUrl' => 'https://billing.stripe.com/session/test_portal_session',
        ]);
    }

    public function testUsesCustomReturnUrl(): void
    {
        $client = self::createClient();
        $container = $client->getContainer();

        $customReturnUrl = 'https://example.com/my-account';

        // Mock Stripe client
        $mockStripeClient = $this->createMock(StripeClientInterface::class);
        $mockStripeClient
            ->method('createPortalSession')
            ->with(SubscriptionFixture::ACTIVE_STRIPE_CUSTOMER_ID, $customReturnUrl)
            ->willReturn(new PortalSessionResult(
                url: 'https://billing.stripe.com/session/test_portal_session_custom',
            ));
        $container->set(StripeClientInterface::class, $mockStripeClient);

        $jwt = TestingLogin::getJwt($client, UserFixture::USER_2_EMAIL);

        $client->request('POST', '/api/subscription/portal', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $jwt,
            ],
            'json' => [
                'returnUrl' => $customReturnUrl,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'portalUrl' => 'https://billing.stripe.com/session/test_portal_session_custom',
        ]);
    }

    public function testFailsForUserWithoutStripeCustomer(): void
    {
        $client = self::createClient();

        // Use admin user who doesn't have a Stripe customer ID
        $jwt = TestingLogin::getJwt($client, 'admin@example.com');

        $client->request('POST', '/api/subscription/portal', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $jwt,
            ],
            'json' => [],
        ]);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testValidatesReturnUrl(): void
    {
        $client = self::createClient();

        $jwt = TestingLogin::getJwt($client, UserFixture::USER_2_EMAIL);

        $client->request('POST', '/api/subscription/portal', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $jwt,
            ],
            'json' => [
                'returnUrl' => 'not-a-valid-url',
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }
}
