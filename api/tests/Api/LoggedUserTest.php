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
                'rank' => 3,
                'challengesAnswered' => 3,
                'points' => 1600,
                'skills' => [
                    [
                        'name' => 'Analytical',
                        'percentage' => 0,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Strategic Planning',
                        'percentage' => 0,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Adaptability',
                        'percentage' => 0,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Premier League Knowledge',
                        'percentage' => 0,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Risk Management',
                        'percentage' => 0,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Decision Making Under Pressure',
                        'percentage' => 0,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Financial Management',
                        'percentage' => 0,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Long Term Vision',
                        'percentage' => 0,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Discipline',
                        'percentage' => 50,
                        'percentageChange' => NULL,
                    ],
                ],
            ],
            'seasonsStatistics' => [
                [
                    'seasonNumber' => 1,
                    'rank' => 3,
                    'challengesAnswered' => 3,
                    'points' => 1600,
                    'skills' => [],
                ],
            ],
        ]);
    }
}
