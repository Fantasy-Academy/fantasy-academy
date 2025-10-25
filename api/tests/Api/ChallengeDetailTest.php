<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Tests\DataFixtures\CurrentChallenge1Fixture;
use FantasyAcademy\API\Tests\DataFixtures\CurrentChallenge2Fixture;
use FantasyAcademy\API\Tests\DataFixtures\ExpiredChallengeFixture;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;
use FantasyAcademy\API\Tests\TestingLogin;

/**
 * @covers \FantasyAcademy\API\Api\ChallengeDetail\ChallengeDetailProvider
 * @covers \FantasyAcademy\API\Api\ChallengeDetail\ChallengeDetailResponse
 */
final class ChallengeDetailTest extends ApiTestCase
{
    public function testChallengeDetailAsUnauthenticatedUser(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/challenges/' . ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'id' => ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID,
            'name' => 'Some expired challenge',
            'shortDescription' => 'Very short description about the expired challenge',
            'isStarted' => true,
            'isExpired' => true,
            'isAnswered' => false,
            'isEvaluated' => true,
        ]);
    }

    public function testChallengeDetailForExpiredEvaluatedChallenge(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_1_EMAIL);

        $response = $client->request('GET', '/api/challenges/' . ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'id' => ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID,
            'name' => 'Some expired challenge',
            'shortDescription' => 'Very short description about the expired challenge',
            'maxPoints' => 1000,
            'isStarted' => true,
            'isExpired' => true,
            'isAnswered' => true,
            'isEvaluated' => true,
            'hintText' => 'Something not that helpful',
        ]);

        // Verify the question is included
        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData['questions']);
        $this->assertCount(1, $responseData['questions']);
        $this->assertEquals(ExpiredChallengeFixture::QUESTION_7_ID, $responseData['questions'][0]['id']);
    }

    public function testChallengeDetailForCurrentAnsweredChallenge(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_3_EMAIL);

        $response = $client->request('GET', '/api/challenges/' . CurrentChallenge1Fixture::CURRENT_CHALLENGE_1_ID, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'id' => CurrentChallenge1Fixture::CURRENT_CHALLENGE_1_ID,
            'name' => 'Some exciting challenge',
            'shortDescription' => 'Very short description about the challenge',
            'maxPoints' => 1000,
            'isStarted' => true,
            'isExpired' => false,
            'isAnswered' => true,
            'isEvaluated' => false,
        ]);

        // Verify questions are included
        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData['questions']);
        $this->assertCount(3, $responseData['questions']);
    }

    public function testChallengeDetailForCurrentUnansweredChallenge(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_1_EMAIL);

        $response = $client->request('GET', '/api/challenges/' . CurrentChallenge2Fixture::CURRENT_CHALLENGE_2_ID, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'id' => CurrentChallenge2Fixture::CURRENT_CHALLENGE_2_ID,
            'name' => 'Another exciting challenge',
            'shortDescription' => 'Very short description about the challenge',
            'maxPoints' => 1000,
            'isStarted' => true,
            'isExpired' => false,
            'isAnswered' => false,
            'isEvaluated' => false,
        ]);

        // Verify questions are included
        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData['questions']);
        $this->assertCount(1, $responseData['questions']);
    }
}
