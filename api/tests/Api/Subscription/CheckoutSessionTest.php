<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api\Subscription;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Services\Stripe\StripeClientInterface;
use FantasyAcademy\API\Services\Stripe\Value\CheckoutSessionResult;
use FantasyAcademy\API\Services\Stripe\Value\CustomerResult;
use FantasyAcademy\API\Services\Stripe\Value\PriceResult;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;
use FantasyAcademy\API\Tests\TestingLogin;

/**
 * @covers \FantasyAcademy\API\MessageHandler\Subscription\CreateCheckoutSessionHandler
 * @covers \FantasyAcademy\API\Message\Subscription\CreateCheckoutSession
 * @covers \FantasyAcademy\API\Api\Subscription\CheckoutSessionResponse
 */
final class CheckoutSessionTest extends ApiTestCase
{
    public function testRequiresAuthentication(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/subscription/checkout', [
            'json' => [
                'plan' => 'monthly',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreatesCheckoutSessionForMonthlyPlan(): void
    {
        $client = self::createClient();
        $container = $client->getContainer();

        // Mock the Stripe client
        $stripeClientMock = $this->createMock(StripeClientInterface::class);

        $stripeClientMock
            ->method('createCustomer')
            ->willReturn(new CustomerResult(
                customerId: 'cus_test_123',
                email: 'admin@example.com',
            ));

        $stripeClientMock
            ->method('getPricesByLookupKeys')
            ->with('fantasy_academy_monthly')
            ->willReturn([
                new PriceResult(
                    priceId: 'price_monthly_test',
                    productId: 'prod_test',
                    unitAmount: 999,
                    currency: 'eur',
                    interval: 'month',
                    lookupKey: 'fantasy_academy_monthly',
                ),
            ]);

        $stripeClientMock
            ->method('createCheckoutSession')
            ->with(
                'cus_test_123',
                'price_monthly_test',
                $this->stringContains('/subscription/success'),
                $this->stringContains('/subscription/cancel'),
            )
            ->willReturn(new CheckoutSessionResult(
                sessionId: 'cs_test_session_123',
                url: 'https://checkout.stripe.com/pay/cs_test_session_123',
            ));

        $container->set(StripeClientInterface::class, $stripeClientMock);

        // USER_1 (admin) has no stripeCustomerId, so it will create one
        $token = TestingLogin::getJwt($client, UserFixture::USER_1_EMAIL);

        $client->request('POST', '/api/subscription/checkout', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'plan' => 'monthly',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'checkoutUrl' => 'https://checkout.stripe.com/pay/cs_test_session_123',
            'sessionId' => 'cs_test_session_123',
        ]);
    }

    public function testCreatesCheckoutSessionForYearlyPlan(): void
    {
        $client = self::createClient();
        $container = $client->getContainer();

        // Mock the Stripe client
        $stripeClientMock = $this->createMock(StripeClientInterface::class);

        $stripeClientMock
            ->method('createCustomer')
            ->willReturn(new CustomerResult(
                customerId: 'cus_test_456',
                email: 'admin@example.com',
            ));

        $stripeClientMock
            ->method('getPricesByLookupKeys')
            ->with('fantasy_academy_yearly')
            ->willReturn([
                new PriceResult(
                    priceId: 'price_yearly_test',
                    productId: 'prod_test',
                    unitAmount: 9990,
                    currency: 'eur',
                    interval: 'year',
                    lookupKey: 'fantasy_academy_yearly',
                ),
            ]);

        $stripeClientMock
            ->method('createCheckoutSession')
            ->willReturn(new CheckoutSessionResult(
                sessionId: 'cs_test_session_456',
                url: 'https://checkout.stripe.com/pay/cs_test_session_456',
            ));

        $container->set(StripeClientInterface::class, $stripeClientMock);

        $token = TestingLogin::getJwt($client, UserFixture::USER_1_EMAIL);

        $client->request('POST', '/api/subscription/checkout', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'plan' => 'yearly',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'checkoutUrl' => 'https://checkout.stripe.com/pay/cs_test_session_456',
            'sessionId' => 'cs_test_session_456',
        ]);
    }

    public function testReusesExistingStripeCustomer(): void
    {
        $client = self::createClient();
        $container = $client->getContainer();

        // Mock the Stripe client
        $stripeClientMock = $this->createMock(StripeClientInterface::class);

        // Should NOT call createCustomer since USER_2 already has stripeCustomerId
        $stripeClientMock
            ->expects($this->never())
            ->method('createCustomer');

        $stripeClientMock
            ->method('getPricesByLookupKeys')
            ->willReturn([
                new PriceResult(
                    priceId: 'price_monthly_test',
                    productId: 'prod_test',
                    unitAmount: 999,
                    currency: 'eur',
                    interval: 'month',
                    lookupKey: 'fantasy_academy_monthly',
                ),
            ]);

        $stripeClientMock
            ->method('createCheckoutSession')
            ->willReturn(new CheckoutSessionResult(
                sessionId: 'cs_test_session_789',
                url: 'https://checkout.stripe.com/pay/cs_test_session_789',
            ));

        $container->set(StripeClientInterface::class, $stripeClientMock);

        // USER_2 already has stripeCustomerId from SubscriptionFixture
        $token = TestingLogin::getJwt($client, UserFixture::USER_2_EMAIL);

        $client->request('POST', '/api/subscription/checkout', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'plan' => 'monthly',
            ],
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testRequiresPlanField(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_1_EMAIL);

        $client->request('POST', '/api/subscription/checkout', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [],
        ]);

        // API returns 400 for missing required constructor parameters
        $this->assertResponseStatusCodeSame(400);
    }

    public function testValidatesPlanValue(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_1_EMAIL);

        $client->request('POST', '/api/subscription/checkout', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'plan' => 'invalid_plan',
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }
}
