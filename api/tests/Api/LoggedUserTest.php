<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;
use FantasyAcademy\API\Tests\TestingLogin;

/**
 * @covers \FantasyAcademy\API\Api\LoggedUser\LoggedUserProvider
 * @covers \FantasyAcademy\API\Api\LoggedUser\LoggedUserResponse
 */
final class LoggedUserTest extends ApiTestCase
{
    public function testPlayerInfoRequiresAuthentication(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/me');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testPlayerInfoReturnsDataForAuthenticatedUser(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_2_EMAIL);

        $client->request('GET', '/api/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'id' => '00000000-0000-0000-0001-000000000002',
            'name' => 'User 2',
            'email' => 'user@example.com',
            'registeredAt' => '2025-05-30T12:00:00+00:00',
            'availableChallenges' => 3,
            'overallStatistics' => [
                'rank' => 2,
                'challengesAnswered' => 6,
                'points' => 3300,
                'skills' => [
                    [
                        'name' => 'Analytical',
                        'percentage' => 67,
                        'percentageChange' => 0,
                    ],
                    [
                        'name' => 'Strategic Planning',
                        'percentage' => 67,
                        'percentageChange' => 0,
                    ],
                    [
                        'name' => 'Adaptability',
                        'percentage' => 67,
                        'percentageChange' => 0,
                    ],
                    [
                        'name' => 'Premier League Knowledge',
                        'percentage' => 67,
                        'percentageChange' => 0,
                    ],
                    [
                        'name' => 'Risk Management',
                        'percentage' => 67,
                        'percentageChange' => 0,
                    ],
                    [
                        'name' => 'Decision Making Under Pressure',
                        'percentage' => 67,
                        'percentageChange' => 0,
                    ],
                    [
                        'name' => 'Financial Management',
                        'percentage' => 67,
                        'percentageChange' => 0,
                    ],
                    [
                        'name' => 'Long Term Vision',
                        'percentage' => 67,
                        'percentageChange' => 0,
                    ],
                    [
                        'name' => 'Discipline',
                        'percentage' => 100,
                        'percentageChange' => 0,
                    ],
                ],
            ],
            'seasonsStatistics' => [
                [
                    'seasonNumber' => 1,
                    'rank' => 2,
                    'challengesAnswered' => 6,
                    'points' => 3300,
                    'skills' => [],
                ],
            ],
            'isMember' => true,
        ]);
    }

    public function testPlayerInfoShowsNotMemberForUserWithoutSubscription(): void
    {
        $client = self::createClient();
        // USER_1 (admin@example.com) has no subscription
        $token = TestingLogin::getJwt($client, UserFixture::USER_1_EMAIL);

        $client->request('GET', '/api/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'isMember' => false,
            'membershipExpiresAt' => null,
        ]);
    }
}
