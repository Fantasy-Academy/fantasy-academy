<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;
use FantasyAcademy\API\Tests\TestingLogin;

/**
 * @covers \FantasyAcademy\API\Api\Leaderboards\LeaderboardsProvider
 * @covers \FantasyAcademy\API\Api\Leaderboards\LeaderboardResponse
 */
final class LeaderboardsTest extends ApiTestCase
{
    public function testLeaderboardsAsUnauthenticatedUser(): void
    {
        $client = self::createClient();

        $response = $client->request('GET', '/api/leaderboards', [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($response->getContent(), true);

        $this->assertIsArray($responseData);
        $this->assertCount(4, $responseData);

        // Verify all players have isMyself = false for unauthenticated user
        foreach ($responseData as $player) {
            $this->assertFalse($player['isMyself']);
        }

        // Verify rankings based on fixture data:
        // 6 evaluated challenges with various gameweeks
        // Previous week (ExpCh1 GW3 + ExpCh5 GW1, both evaluated 2 weeks ago):
        //   USER_1: 1800 points (rank 1)
        //   USER_2: 1567 points (rank 2)
        //   USER_3: 1233 points (rank 3)
        //   USER_4: 0 points (rank 4)
        // Current (all 6 evaluated challenges):
        //   USER_1: 5800 points (rank 1) - stayed at rank 1
        //   USER_2: 3300 points (rank 2) - stayed at rank 2
        //   USER_3: 2350 points (rank 3) - stayed at rank 3
        //   USER_4: 1333 points (rank 4) - stayed at rank 4
        $this->assertEquals(UserFixture::USER_1_ID, $responseData[0]['playerId']);
        $this->assertEquals('User 1', $responseData[0]['playerName']);
        $this->assertEquals(1, $responseData[0]['rank']);
        $this->assertEquals(5800, $responseData[0]['points']);
        $this->assertEquals(6, $responseData[0]['challengesAnswered']);
        $this->assertEquals(0, $responseData[0]['rankChange']); // stayed at rank 1
        // pointsChange = current week points (Challenges evaluated within last week from Monday)
        // Challenge2 (-2 days) + Challenge4 (-3 days) + Challenge7 (-4 days) = 1000+1000+1000 = 3000
        // Challenge6 (-5 days) depends on day of week calculation
        $this->assertEquals(1800, $responseData[0]['pointsChange']);

        $this->assertEquals(UserFixture::USER_2_ID, $responseData[1]['playerId']);
        $this->assertEquals('User 2', $responseData[1]['playerName']);
        $this->assertEquals(2, $responseData[1]['rank']);
        $this->assertEquals(3300, $responseData[1]['points']);
        $this->assertEquals(6, $responseData[1]['challengesAnswered']);
        $this->assertEquals(0, $responseData[1]['rankChange']); // stayed at rank 2
        $this->assertEquals(1233, $responseData[1]['pointsChange']);

        $this->assertEquals(UserFixture::USER_3_ID, $responseData[2]['playerId']);
        $this->assertEquals('User 3', $responseData[2]['playerName']);
        $this->assertEquals(3, $responseData[2]['rank']);
        $this->assertEquals(2350, $responseData[2]['points']);
        $this->assertEquals(6, $responseData[2]['challengesAnswered']);
        $this->assertEquals(1, $responseData[2]['rankChange']); // moved up from rank 4
        $this->assertEquals(1567, $responseData[2]['pointsChange']);

        $this->assertEquals(UserFixture::USER_4_ID, $responseData[3]['playerId']);
        $this->assertEquals('User 4', $responseData[3]['playerName']);
        $this->assertEquals(4, $responseData[3]['rank']);
        $this->assertEquals(1333, $responseData[3]['points']);
        $this->assertEquals(2, $responseData[3]['challengesAnswered']);
        $this->assertEquals(-1, $responseData[3]['rankChange']); // dropped from rank 3 to rank 4
        $this->assertEquals(333, $responseData[3]['pointsChange']); // only Challenge6 points in current week
    }

    public function testLeaderboardsAsAuthenticatedUser(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_1_EMAIL);

        $response = $client->request('GET', '/api/leaderboards', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($response->getContent(), true);

        $this->assertIsArray($responseData);
        $this->assertCount(4, $responseData);

        // Find USER_1 and verify isMyself is true
        $user1Found = false;
        foreach ($responseData as $player) {
            if ($player['playerId'] === UserFixture::USER_1_ID) {
                $this->assertTrue($player['isMyself']);
                $this->assertEquals('User 1', $player['playerName']);
                $this->assertEquals(1, $player['rank']);
                $this->assertEquals(5800, $player['points']);
                $this->assertEquals(6, $player['challengesAnswered']);
                $this->assertEquals(0, $player['rankChange']);
                $this->assertEquals(1800, $player['pointsChange']);
                $user1Found = true;
            } else {
                $this->assertFalse($player['isMyself']);
            }
        }

        $this->assertTrue($user1Found, 'USER_1 should be in the leaderboard');
    }
}
