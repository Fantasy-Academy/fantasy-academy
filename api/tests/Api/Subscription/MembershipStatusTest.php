<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api\Subscription;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;
use FantasyAcademy\API\Tests\TestingLogin;

/**
 * @covers \FantasyAcademy\API\Api\Subscription\MembershipStatusProvider
 * @covers \FantasyAcademy\API\Api\Subscription\MembershipStatusResponse
 */
final class MembershipStatusTest extends ApiTestCase
{
    public function testRequiresAuthentication(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/subscription/status');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testReturnsActiveStatusForActiveSubscription(): void
    {
        $client = self::createClient();
        // USER_2 has an active subscription
        $token = TestingLogin::getJwt($client, UserFixture::USER_2_EMAIL);

        $client->request('GET', '/api/subscription/status', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'isActive' => true,
            'status' => 'active',
            'planId' => 'price_monthly_test',
            'willCancelAtPeriodEnd' => false,
        ]);
    }

    public function testReturnsCanceledStatusWithPeriodEnd(): void
    {
        $client = self::createClient();
        // USER_3 has a canceled but still active subscription
        $token = TestingLogin::getJwt($client, UserFixture::USER_3_EMAIL);

        $client->request('GET', '/api/subscription/status', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'isActive' => true,
            'status' => 'active',
            'planId' => 'price_yearly_test',
            'willCancelAtPeriodEnd' => true,
        ]);

        // Also verify these keys exist and are not null by matching structure
        $this->assertMatchesJsonSchema([
            'type' => 'object',
            'required' => ['isActive', 'status', 'planId', 'currentPeriodEnd', 'canceledAt', 'willCancelAtPeriodEnd'],
            'properties' => [
                'canceledAt' => ['type' => 'string'],
                'currentPeriodEnd' => ['type' => 'string'],
            ],
        ]);
    }

    public function testReturnsInactiveForExpiredSubscription(): void
    {
        $client = self::createClient();
        // USER_4 has an expired subscription
        $token = TestingLogin::getJwt($client, UserFixture::USER_4_EMAIL);

        $client->request('GET', '/api/subscription/status', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'isActive' => false,
            'status' => null,
            'planId' => null,
            'currentPeriodEnd' => null,
            'canceledAt' => null,
            'willCancelAtPeriodEnd' => false,
        ]);
    }

    public function testReturnsInactiveForNoSubscription(): void
    {
        $client = self::createClient();
        // USER_1 (admin) has no subscription
        $token = TestingLogin::getJwt($client, UserFixture::USER_1_EMAIL);

        $client->request('GET', '/api/subscription/status', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'isActive' => false,
            'status' => null,
            'planId' => null,
            'currentPeriodEnd' => null,
            'canceledAt' => null,
            'willCancelAtPeriodEnd' => false,
        ]);
    }
}
