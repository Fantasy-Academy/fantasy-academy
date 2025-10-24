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
            'id' => UserFixture::USER_2_ID,
            'name' => 'User 2',
            'email' => UserFixture::USER_2_EMAIL,
            'availableChallenges' => 4,
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
}
