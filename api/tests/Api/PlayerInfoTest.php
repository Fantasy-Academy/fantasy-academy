<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;
use FantasyAcademy\API\Tests\TestingLogin;
use FantasyAcademy\API\Value\PlayerStatistics;

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
                'rankChange' => 0,
                'pointsChange' => 800,
                'skills' => [
                    [
                        'name' => 'Analytical',
                        'percentage' => 50,
                        'percentageChange' => -50,
                    ],
                    [
                        'name' => 'Strategic Planning',
                        'percentage' => 50,
                        'percentageChange' => -50,
                    ],
                    [
                        'name' => 'Adaptability',
                        'percentage' => 50,
                        'percentageChange' => -50,
                    ],
                    [
                        'name' => 'Premier League Knowledge',
                        'percentage' => 50,
                        'percentageChange' => -50,
                    ],
                    [
                        'name' => 'Risk Management',
                        'percentage' => 50,
                        'percentageChange' => -50,
                    ],
                    [
                        'name' => 'Decision Making Under Pressure',
                        'percentage' => 50,
                        'percentageChange' => -50,
                    ],
                    [
                        'name' => 'Financial Management',
                        'percentage' => 50,
                        'percentageChange' => -50,
                    ],
                    [
                        'name' => 'Long Term Vision',
                        'percentage' => 50,
                        'percentageChange' => -50,
                    ],
                    [
                        'name' => 'Discipline',
                        'percentage' => 67,
                        'percentageChange' => -33,
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

    public function testPlayerStatisticsSupportsNegativeChanges(): void
    {
        // This test verifies that PlayerStatistics can handle negative changes
        // even though it may be a rare scenario in production
        $statistics = new PlayerStatistics(
            rank: 10,
            challengesAnswered: 5,
            points: 500,
            skills: [],
            rankChange: -5,  // rank got worse (was 5, now 10)
            pointsChange: -300,  // points decreased (had 800, now 500)
        );

        $this->assertEquals(-5, $statistics->rankChange);
        $this->assertEquals(-300, $statistics->pointsChange);
    }
}
