<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Tests\DataFixtures\CurrentChallenge1Fixture;
use FantasyAcademy\API\Tests\DataFixtures\CurrentChallenge2Fixture;
use FantasyAcademy\API\Tests\DataFixtures\ExpiredChallenge2Fixture;
use FantasyAcademy\API\Tests\DataFixtures\ExpiredChallengeFixture;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;
use FantasyAcademy\API\Tests\TestingLogin;

/**
 * @covers \FantasyAcademy\API\Api\Challenges\ChallengesProvider
 * @covers \FantasyAcademy\API\Api\Challenges\ChallengeResponse
 */
final class ChallengesTest extends ApiTestCase
{
    public function testChallengesAsUnauthenticatedUser(): void
    {
        $client = self::createClient();

        $response = $client->request('GET', '/api/challenges', [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($response->getContent(), true);

        $this->assertIsArray($responseData);
        $this->assertCount(10, $responseData);

        // Verify all challenges have isAnswered = false and myPoints = null for unauthenticated user
        foreach ($responseData as $challenge) {
            $this->assertFalse($challenge['isAnswered']);
            $this->assertNull($challenge['myPoints']);
        }
    }

    public function testChallengesAsAuthenticatedUser(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_1_EMAIL);

        $response = $client->request('GET', '/api/challenges', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($response->getContent(), true);

        $this->assertIsArray($responseData);
        $this->assertCount(10, $responseData);

        // Find specific challenges and verify their answered status
        $challengesById = [];
        foreach ($responseData as $challenge) {
            $challengesById[$challenge['id']] = $challenge;
        }

        // USER_1 answered ExpiredChallengeFixture and ExpiredChallenge2Fixture
        $this->assertTrue($challengesById[ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]['isAnswered']);
        $this->assertTrue($challengesById[ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID]['isAnswered']);
        $this->assertTrue($challengesById[ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]['isEvaluated']);
        $this->assertTrue($challengesById[ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID]['isEvaluated']);

        // USER_1 did not answer CurrentChallenge1, but did answer CurrentChallenge2
        $this->assertFalse($challengesById[CurrentChallenge1Fixture::CURRENT_CHALLENGE_1_ID]['isAnswered']);
        $this->assertTrue($challengesById[CurrentChallenge2Fixture::CURRENT_CHALLENGE_2_ID]['isAnswered']);
        $this->assertFalse($challengesById[CurrentChallenge1Fixture::CURRENT_CHALLENGE_1_ID]['isEvaluated']);
        $this->assertFalse($challengesById[CurrentChallenge2Fixture::CURRENT_CHALLENGE_2_ID]['isEvaluated']);

        // Verify myPoints for evaluated challenges
        $this->assertEquals(800, $challengesById[ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]['myPoints']);
        $this->assertIsInt($challengesById[ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID]['myPoints']);

        // Verify myPoints is null for unevaluated challenges
        $this->assertNull($challengesById[CurrentChallenge1Fixture::CURRENT_CHALLENGE_1_ID]['myPoints']);
        $this->assertNull($challengesById[CurrentChallenge2Fixture::CURRENT_CHALLENGE_2_ID]['myPoints']);
    }
}
