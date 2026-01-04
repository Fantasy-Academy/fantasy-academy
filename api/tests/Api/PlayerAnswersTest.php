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

        // User 1 has 6 evaluated challenges: ExpiredChallenge1, 2, 4, 5, 6, 7
        // ExpiredChallenge3 is NOT evaluated, so should not appear
        $this->assertCount(6, $responseData['challenges']);

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

        // Verify correctAnswer for Question 7 (Text question)
        $this->assertArrayHasKey('correctAnswer', $question);
        $this->assertIsArray($question['correctAnswer']);
        $this->assertArrayHasKey('textAnswer', $question['correctAnswer']);
        $this->assertEquals('This is the correct text answer', $question['correctAnswer']['textAnswer']);

        // Verify ExpiredChallenge2 data
        $this->assertEquals('Another expired challenge', $expiredChallenge2Data['challengeName']);
        $this->assertEquals(1000, $expiredChallenge2Data['points']); // User 1 got 1000 points (all correct)
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

        // Verify choice texts are included for choice-based questions
        $question8 = null;
        $question9 = null;
        foreach ($expiredChallenge2Data['questions'] as $question) {
            if ($question['questionId'] === ExpiredChallenge2Fixture::QUESTION_8_ID) {
                $question8 = $question;
            } elseif ($question['questionId'] === ExpiredChallenge2Fixture::QUESTION_9_ID) {
                $question9 = $question;
            }
        }

        $this->assertNotNull($question8, 'Question 8 should be present');
        $this->assertNotNull($question9, 'Question 9 should be present');

        // Question 8 (SingleSelect) - User 1 selected Red (CHOICE_9_ID)
        $this->assertEquals(ExpiredChallenge2Fixture::CHOICE_9_ID, $question8['answer']['selectedChoiceId']);
        $this->assertEquals('Red', $question8['answer']['selectedChoiceText']);

        // Verify correctAnswer for Question 8 (SingleSelect) - Correct answer is Red (CHOICE_9_ID)
        assert(is_array($question8));
        $this->assertArrayHasKey('correctAnswer', $question8);
        $this->assertIsArray($question8['correctAnswer']);
        $this->assertEquals(ExpiredChallenge2Fixture::CHOICE_9_ID, $question8['correctAnswer']['selectedChoiceId']);
        $this->assertEquals('Red', $question8['correctAnswer']['selectedChoiceText']);

        // Question 9 (MultiSelect) - User 1 selected 7 and 13 (CHOICE_12_ID, CHOICE_13_ID)
        $this->assertIsArray($question9['answer']['selectedChoiceIds']);
        $this->assertCount(2, $question9['answer']['selectedChoiceIds']);
        $this->assertEquals(ExpiredChallenge2Fixture::CHOICE_12_ID, $question9['answer']['selectedChoiceIds'][0]);
        $this->assertEquals(ExpiredChallenge2Fixture::CHOICE_13_ID, $question9['answer']['selectedChoiceIds'][1]);
        $this->assertIsArray($question9['answer']['selectedChoiceTexts']);
        $this->assertCount(2, $question9['answer']['selectedChoiceTexts']);
        $this->assertEquals('7', $question9['answer']['selectedChoiceTexts'][0]);
        $this->assertEquals('13', $question9['answer']['selectedChoiceTexts'][1]);

        // Verify correctAnswer for Question 9 (MultiSelect) - Correct answers are 7 and 13
        assert(is_array($question9));
        $this->assertArrayHasKey('correctAnswer', $question9);
        $this->assertIsArray($question9['correctAnswer']);
        $this->assertIsArray($question9['correctAnswer']['selectedChoiceIds']);
        $this->assertCount(2, $question9['correctAnswer']['selectedChoiceIds']);
        $this->assertEquals(ExpiredChallenge2Fixture::CHOICE_12_ID, $question9['correctAnswer']['selectedChoiceIds'][0]);
        $this->assertEquals(ExpiredChallenge2Fixture::CHOICE_13_ID, $question9['correctAnswer']['selectedChoiceIds'][1]);
        $this->assertIsArray($question9['correctAnswer']['selectedChoiceTexts']);
        $this->assertCount(2, $question9['correctAnswer']['selectedChoiceTexts']);
        $this->assertEquals('7', $question9['correctAnswer']['selectedChoiceTexts'][0]);
        $this->assertEquals('13', $question9['correctAnswer']['selectedChoiceTexts'][1]);

        // Verify correctAnswer for Question 10 (Numeric) - Correct answer is 42.0
        $question10 = null;
        foreach ($expiredChallenge2Data['questions'] as $question) {
            if ($question['questionId'] === ExpiredChallenge2Fixture::QUESTION_10_ID) {
                $question10 = $question;
            }
        }
        $this->assertNotNull($question10, 'Question 10 should be present');
        assert(is_array($question10));
        $this->assertArrayHasKey('correctAnswer', $question10);
        $this->assertIsArray($question10['correctAnswer']);
        $this->assertEquals(42.0, $question10['correctAnswer']['numericAnswer']);
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
        // - ExpiredChallenge4, 5, 6, 7 (evaluated) ✓
        // - CurrentChallenge1 (NOT evaluated) ✗
        // - CurrentChallenge2 (NOT evaluated) ✗
        // So only 6 evaluated challenges should appear
        $this->assertCount(6, $responseData['challenges']);

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
        $this->assertCount(2, $responseData['challenges']); // User 4 answered ExpiredChallenge6 and ExpiredChallenge7
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
        $this->assertCount(6, $responseData['challenges']); // Same data as when authenticated
    }
}
