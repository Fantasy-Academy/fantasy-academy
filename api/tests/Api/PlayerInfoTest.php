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
                'rank' => 4,
                'challengesAnswered' => 2,
                'points' => 1333,
            ],
            'seasonsStatistics' => [
                [
                    'seasonNumber' => 1,
                    'rank' => 4,
                    'challengesAnswered' => 2,
                    'points' => 1333,
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
                'rank' => 3,
                'challengesAnswered' => 6,
                'points' => 2350,
                'rankChange' => 1,
                'pointsChange' => 1567,
            ],
            'seasonsStatistics' => [
                [
                    'seasonNumber' => 1,
                    'rank' => 3,
                    'challengesAnswered' => 6,
                    'points' => 2350,
                    'skills' => [],
                ],
            ],
        ]);
    }
}
