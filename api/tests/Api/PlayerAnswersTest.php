<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Tests\DataFixtures\ExpiredChallenge2Fixture;
use FantasyAcademy\API\Tests\DataFixtures\ExpiredChallengeFixture;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;
use FantasyAcademy\API\Tests\TestingLogin;

/**
 * @covers \FantasyAcademy\API\Api\PlayerAnswers\PlayerAnswersProvider
 * @covers \FantasyAcademy\API\Api\PlayerAnswers\PlayerAnswersResponse
 */
final class PlayerAnswersTest extends ApiTestCase
{
    public function testPlayerAnswersReturnsEvaluatedChallengesOrderedByEvaluatedAt(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_1_EMAIL);

        $response = $client->request('GET', '/api/players/' . UserFixture::USER_1_ID . '/answers', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData);

        // Verify top-level structure
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals(UserFixture::USER_1_ID, $responseData['id']);
        $this->assertArrayHasKey('challenges', $responseData);
        $this->assertIsArray($responseData['challenges']);

        // User 1 has 2 evaluated challenges: ExpiredChallenge1 and ExpiredChallenge2
        // ExpiredChallenge3 is NOT evaluated, so should not appear
        $this->assertCount(2, $responseData['challenges']);

        $challenges = $responseData['challenges'];

        // Verify challenges are ordered by evaluated_at DESC
        // Both were evaluated at the same time in fixtures, so order by challenge creation/ID
        // ExpiredChallenge2 should come first (evaluated second in fixture)
        $challenge1 = $challenges[0];
        $challenge2 = $challenges[1];

        // Verify first challenge structure
        $this->assertIsArray($challenge1);
        $this->assertArrayHasKey('challengeId', $challenge1);
        $this->assertArrayHasKey('challengeName', $challenge1);
        $this->assertArrayHasKey('points', $challenge1);
        $this->assertArrayHasKey('questions', $challenge1);

        // Verify second challenge structure
        $this->assertIsArray($challenge2);
        $this->assertArrayHasKey('challengeId', $challenge2);
        $this->assertArrayHasKey('challengeName', $challenge2);
        $this->assertArrayHasKey('points', $challenge2);
        $this->assertArrayHasKey('questions', $challenge2);

        // Find ExpiredChallenge1 and ExpiredChallenge2 in results
        $expiredChallenge1Data = null;
        $expiredChallenge2Data = null;

        foreach ($challenges as $challenge) {
            if ($challenge['challengeId'] === ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID) {
                $expiredChallenge1Data = $challenge;
            } elseif ($challenge['challengeId'] === ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID) {
                $expiredChallenge2Data = $challenge;
            }
        }

        $this->assertNotNull($expiredChallenge1Data, 'ExpiredChallenge1 should be in results');
        $this->assertNotNull($expiredChallenge2Data, 'ExpiredChallenge2 should be in results');

        // Verify ExpiredChallenge1 data
        $this->assertEquals('Some expired challenge', $expiredChallenge1Data['challengeName']);
        $this->assertEquals(800, $expiredChallenge1Data['points']); // User 1 got 800 points
        $this->assertIsArray($expiredChallenge1Data['questions']);
        $this->assertCount(1, $expiredChallenge1Data['questions']); // 1 question in ExpiredChallenge1

        // Verify question data for ExpiredChallenge1
        $question = $expiredChallenge1Data['questions'][0];
        $this->assertIsArray($question);
        $this->assertArrayHasKey('questionId', $question);
        $this->assertEquals(ExpiredChallengeFixture::QUESTION_7_ID, $question['questionId']);
        $this->assertArrayHasKey('questionText', $question);
        $this->assertEquals('Some dummy expired question', $question['questionText']);
        $this->assertArrayHasKey('answer', $question);
        $this->assertIsArray($question['answer']);
        $this->assertArrayHasKey('textAnswer', $question['answer']);
        $this->assertEquals('User 1 answer to question 7', $question['answer']['textAnswer']);

        // Verify ExpiredChallenge2 data
        $this->assertEquals('Another expired challenge', $expiredChallenge2Data['challengeName']);
        $this->assertEquals(600, $expiredChallenge2Data['points']); // User 1 got 600 points
        $this->assertIsArray($expiredChallenge2Data['questions']);
        $this->assertCount(3, $expiredChallenge2Data['questions']); // 3 questions in ExpiredChallenge2

        // Verify all questions have proper structure
        foreach ($expiredChallenge2Data['questions'] as $question) {
            $this->assertIsArray($question);
            $this->assertArrayHasKey('questionId', $question);
            $this->assertArrayHasKey('questionText', $question);
            $this->assertArrayHasKey('answer', $question);
            $this->assertIsArray($question['answer']);
        }
    }

    public function testPlayerAnswersDoesNotIncludeUnevaluatedChallenges(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_3_EMAIL);

        $response = $client->request('GET', '/api/players/' . UserFixture::USER_3_ID . '/answers', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertEquals(UserFixture::USER_3_ID, $responseData['id']);
        $this->assertIsArray($responseData['challenges']);

        // User 3 answered:
        // - ExpiredChallenge1 (evaluated) ✓
        // - ExpiredChallenge2 (evaluated) ✓
        // - CurrentChallenge1 (NOT evaluated) ✗
        // - CurrentChallenge2 (NOT evaluated) ✗
        // So only 2 evaluated challenges should appear
        $this->assertCount(2, $responseData['challenges']);

        // Verify both challenges are the evaluated ones
        $challengeIds = array_map(fn($challenge) => $challenge['challengeId'], $responseData['challenges']);
        $this->assertContains(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID, $challengeIds);
        $this->assertContains(ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID, $challengeIds);
    }

    public function testPlayerAnswersReturnsEmptyArrayForPlayerWithNoEvaluatedChallenges(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_4_EMAIL);

        $response = $client->request('GET', '/api/players/' . UserFixture::USER_4_ID . '/answers', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertEquals(UserFixture::USER_4_ID, $responseData['id']);
        $this->assertArrayHasKey('challenges', $responseData);
        $this->assertIsArray($responseData['challenges']);
        $this->assertCount(0, $responseData['challenges']); // User 4 has no answers
    }

    public function testPlayerAnswersWorksAsUnauthenticated(): void
    {
        $client = self::createClient();

        $response = $client->request('GET', '/api/players/' . UserFixture::USER_1_ID . '/answers', [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertEquals(UserFixture::USER_1_ID, $responseData['id']);
        $this->assertArrayHasKey('challenges', $responseData);
        $this->assertIsArray($responseData['challenges']);
        $this->assertCount(2, $responseData['challenges']); // Same data as when authenticated
    }
}
