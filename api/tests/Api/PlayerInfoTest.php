<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;
use FantasyAcademy\API\Tests\TestingLogin;

/**
 * @covers \FantasyAcademy\API\Api\PlayerInfo\PlayerInfoProvider
 * @covers \FantasyAcademy\API\Api\PlayerInfo\PlayerInfoResponse
 */
final class PlayerInfoTest extends ApiTestCase
{
    public function testPlayerInfoReturnsData(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/player/' . UserFixture::USER_4_ID, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'id' => UserFixture::USER_4_ID,
            'isMyself' => false,
            'name' => 'User 4',
            'registeredAt' => '2025-05-30T12:00:00+00:00',
            'overallStatistics' => [
                'rank' => null,
                'challengesAnswered' => 0,
                'points' => 0,
                'skills' => [],
            ],
            'seasonsStatistics' => [
                [
                    'seasonNumber' => 1,
                    'rank' => null,
                    'challengesAnswered' => 0,
                    'points' => 0,
                    'skills' => [],
                ],
            ],
        ]);
    }

    public function testPlayerInfoReturnsDataWithMyself(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_3_EMAIL);

        $client->request('GET', '/api/player/' . UserFixture::USER_3_ID, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'id' => UserFixture::USER_3_ID,
            'isMyself' => true,
            'name' => 'User 3',
            'registeredAt' => '2025-05-30T12:00:00+00:00',
            'overallStatistics' => [
                'rank' => 2,
                'challengesAnswered' => 4,
                'points' => 1700,
                'skills' => [
                    [
                        'name' => 'Analytical',
                        'percentage' => 50,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Strategic Planning',
                        'percentage' => 50,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Adaptability',
                        'percentage' => 50,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Premier League Knowledge',
                        'percentage' => 50,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Risk Management',
                        'percentage' => 50,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Decision Making Under Pressure',
                        'percentage' => 50,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Financial Management',
                        'percentage' => 50,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Long Term Vision',
                        'percentage' => 50,
                        'percentageChange' => NULL,
                    ],
                    [
                        'name' => 'Discipline',
                        'percentage' => 67,
                        'percentageChange' => NULL,
                    ],
                ],
            ],
            'seasonsStatistics' => [
                [
                    'seasonNumber' => 1,
                    'rank' => 2,
                    'challengesAnswered' => 4,
                    'points' => 1700,
                    'skills' => [],
                ],
            ],
        ]);
    }
}
