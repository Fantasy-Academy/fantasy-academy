<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;
use FantasyAcademy\API\Tests\TestingLogin;

final class PlayerInfoTest extends ApiTestCase
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
            'registeredAt' => '2025-10-17T22:26:37+00:00',
            'availableChallenges' => 3,
            'overallStatistics' => [
                'rank' => 2,
                'challengesAnswered' => 2,
                'points' => 0,
                'skills' => [
                    [
                        'name' => 'Analytical',
                        'percentage' => 100,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Strategic Planning',
                        'percentage' => 100,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Adaptability',
                        'percentage' => 100,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Premier League Knowledge',
                        'percentage' => 100,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Risk Management',
                        'percentage' => 100,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Decision Making Under Pressure',
                        'percentage' => 100,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Financial Management',
                        'percentage' => 100,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Long Term Vision',
                        'percentage' => 100,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Discipline',
                        'percentage' => 40,
                        'percentageChange' => NULL,
                    ],
                ],
            ],
            'seasonsStatistics' => [
                [
                    'seasonNumber' => 1,
                    'rank' => 2,
                    'challengesAnswered' => 2,
                    'points' => 0,
                    'skills' => [],
                ],
            ],
        ]);
    }
}
