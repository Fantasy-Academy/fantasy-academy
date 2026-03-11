<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;

/**
 * @covers \FantasyAcademy\API\Api\PlayerActivity\PlayerActivityProvider
 * @covers \FantasyAcademy\API\Api\PlayerActivity\PlayerActivityResponse
 */
final class PlayerActivityTest extends ApiTestCase
{
    public function testPlayerWithFullActivityHasPerfectStreaks(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/player/' . UserFixture::USER_3_ID . '/activity', [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'id' => UserFixture::USER_3_ID,
            'overallActivity' => 1.0,
            'currentStreak' => 3,
            'longestStreak' => 3,
            'gameweeks' => [
                [
                    'gameweek' => 1,
                    'totalChallenges' => 1,
                    'answeredChallenges' => 1,
                    'activity' => 1.0,
                ],
                [
                    'gameweek' => 2,
                    'totalChallenges' => 3,
                    'answeredChallenges' => 3,
                    'activity' => 1.0,
                ],
                [
                    'gameweek' => 3,
                    'totalChallenges' => 2,
                    'answeredChallenges' => 2,
                    'activity' => 1.0,
                ],
            ],
        ]);
    }

    public function testPlayerWithPartialActivityHasCorrectRatios(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/player/' . UserFixture::USER_4_ID . '/activity', [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            'id' => UserFixture::USER_4_ID,
            'currentStreak' => 0,
            'longestStreak' => 0,
            'gameweeks' => [
                [
                    'gameweek' => 1,
                    'totalChallenges' => 1,
                    'answeredChallenges' => 0,
                    'activity' => 0.0,
                ],
                [
                    'gameweek' => 2,
                    'totalChallenges' => 3,
                    'answeredChallenges' => 1,
                    'activity' => 0.3333,
                ],
                [
                    'gameweek' => 3,
                    'totalChallenges' => 2,
                    'answeredChallenges' => 1,
                    'activity' => 0.5,
                ],
            ],
        ]);
    }
}
