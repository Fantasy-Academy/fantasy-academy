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
        // Previous week (>1 week ago - Challenge 1):
        //   USER_2: 900 points (rank 1)
        //   USER_3: 900 points (rank 2 - alphabetically after User 2)
        //   USER_1: 800 points (rank 3)
        //   USER_4: 0 points (rank 4)
        // Current week (all challenges):
        //   USER_1: 1900 points (rank 1) - gained 1100 points, rank improved by 2
        //   USER_3: 1700 points (rank 2) - gained 800 points, rank stayed same (0)
        //   USER_2: 1600 points (rank 3) - gained 700 points, rank dropped by 2
        //   USER_4: 0 points (rank 4) - no change
        $this->assertEquals(UserFixture::USER_1_ID, $responseData[0]['playerId']);
        $this->assertEquals('User 1', $responseData[0]['playerName']);
        $this->assertEquals(1, $responseData[0]['rank']);
        $this->assertEquals(1900, $responseData[0]['points']);
        $this->assertEquals(4, $responseData[0]['challengesAnswered']);
        $this->assertEquals(2, $responseData[0]['rankChange']); // improved from rank 3 to rank 1
        $this->assertEquals(1100, $responseData[0]['pointsChange']); // gained 1100 points (600 + 500)

        $this->assertEquals(UserFixture::USER_3_ID, $responseData[1]['playerId']);
        $this->assertEquals('User 3', $responseData[1]['playerName']);
        $this->assertEquals(2, $responseData[1]['rank']);
        $this->assertEquals(1700, $responseData[1]['points']);
        $this->assertEquals(4, $responseData[1]['challengesAnswered']);
        $this->assertEquals(0, $responseData[1]['rankChange']); // stayed at rank 2
        $this->assertEquals(800, $responseData[1]['pointsChange']); // gained 800 points

        $this->assertEquals(UserFixture::USER_2_ID, $responseData[2]['playerId']);
        $this->assertEquals('User 2', $responseData[2]['playerName']);
        $this->assertEquals(3, $responseData[2]['rank']);
        $this->assertEquals(1600, $responseData[2]['points']);
        $this->assertEquals(3, $responseData[2]['challengesAnswered']);
        $this->assertEquals(-2, $responseData[2]['rankChange']); // dropped from rank 1 to rank 3
        $this->assertEquals(700, $responseData[2]['pointsChange']); // gained 700 points

        $this->assertEquals(UserFixture::USER_4_ID, $responseData[3]['playerId']);
        $this->assertEquals('User 4', $responseData[3]['playerName']);
        $this->assertEquals(4, $responseData[3]['rank']);
        $this->assertEquals(0, $responseData[3]['points']);
        $this->assertEquals(0, $responseData[3]['challengesAnswered']);
        $this->assertEquals(0, $responseData[3]['rankChange']); // stayed at rank 4
        $this->assertEquals(0, $responseData[3]['pointsChange']); // no points gained
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
                $this->assertEquals(1900, $player['points']);
                $this->assertEquals(4, $player['challengesAnswered']);
                $this->assertEquals(2, $player['rankChange']);
                $this->assertEquals(1100, $player['pointsChange']);
                $user1Found = true;
            } else {
                $this->assertFalse($player['isMyself']);
            }
        }

        $this->assertTrue($user1Found, 'USER_1 should be in the leaderboard');
    }
}
