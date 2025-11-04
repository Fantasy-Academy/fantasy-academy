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
            'myPoints' => null,
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
            'myPoints' => 800,
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
            'myPoints' => null,
        ]);

        // Verify questions are included
        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData['questions']);
        $this->assertCount(3, $responseData['questions']);
    }

    public function testChallengeDetailForCurrentUnansweredChallenge(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_4_EMAIL);

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
            'myPoints' => null,
        ]);

        // Verify questions are included
        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData['questions']);
        $this->assertCount(1, $responseData['questions']);
    }

    public function testStatisticsShownForEvaluatedChallenge(): void
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

        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData['questions']);
        $this->assertCount(1, $responseData['questions']);

        // Verify statistics are present for evaluated challenge
        $question = $responseData['questions'][0];
        $this->assertIsArray($question);
        $this->assertArrayHasKey('statistics', $question);
        $this->assertIsArray($question['statistics']);
        $this->assertArrayHasKey('totalAnswers', $question['statistics']);
        $this->assertArrayHasKey('answers', $question['statistics']);

        // Question 7 has 3 text answers from 3 users
        $this->assertEquals(3, $question['statistics']['totalAnswers']);
        $this->assertIsArray($question['statistics']['answers']);
        $this->assertCount(3, $question['statistics']['answers']);

        // Verify each answer has the correct structure
        foreach ($question['statistics']['answers'] as $answerStat) {
            $this->assertIsArray($answerStat);
            $this->assertArrayHasKey('answer', $answerStat);
            $this->assertArrayHasKey('count', $answerStat);
            $this->assertArrayHasKey('percentage', $answerStat);
            $this->assertIsArray($answerStat['answer']);
            $this->assertIsInt($answerStat['count']);
            $this->assertIsFloat($answerStat['percentage']);

            // For text answers, verify the textAnswer field is populated
            $this->assertArrayHasKey('textAnswer', $answerStat['answer']);
            $this->assertIsString($answerStat['answer']['textAnswer']);
        }

        // Verify correct answer is present
        $this->assertArrayHasKey('correctAnswer', $question);
        $this->assertIsArray($question['correctAnswer']);
        $this->assertArrayHasKey('textAnswer', $question['correctAnswer']);
        $this->assertEquals('This is the correct text answer', $question['correctAnswer']['textAnswer']);
    }

    public function testStatisticsShownWhenShowStatisticsContinuouslyIsTrue(): void
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

        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData['questions']);
        $this->assertCount(1, $responseData['questions']);

        // Verify statistics are present even though challenge is not evaluated
        $question = $responseData['questions'][0];
        $this->assertIsArray($question);
        $this->assertArrayHasKey('statistics', $question);
        $this->assertIsArray($question['statistics']);
        $this->assertArrayHasKey('totalAnswers', $question['statistics']);
        $this->assertArrayHasKey('answers', $question['statistics']);

        // Question 4 has 3 answers: 2 Red, 1 Blue
        $this->assertEquals(3, $question['statistics']['totalAnswers']);
        $this->assertIsArray($question['statistics']['answers']);
        $this->assertCount(2, $question['statistics']['answers']); // 2 unique answers

        // Find and verify the statistics for each choice
        $redStats = null;
        $blueStats = null;
        foreach ($question['statistics']['answers'] as $answerStat) {
            $this->assertIsArray($answerStat);
            $this->assertArrayHasKey('answer', $answerStat);
            $this->assertIsArray($answerStat['answer']);

            // Check if this answer is for the Red choice
            if (isset($answerStat['answer']['selectedChoiceId']) &&
                $answerStat['answer']['selectedChoiceId'] === CurrentChallenge2Fixture::CHOICE_20_ID) {
                $redStats = $answerStat;
            } elseif (isset($answerStat['answer']['selectedChoiceId']) &&
                      $answerStat['answer']['selectedChoiceId'] === CurrentChallenge2Fixture::CHOICE_21_ID) {
                $blueStats = $answerStat;
            }
        }

        $this->assertNotNull($redStats, 'Red choice statistics should be present');
        $this->assertNotNull($blueStats, 'Blue choice statistics should be present');
        $this->assertEquals(2, $redStats['count']);
        $this->assertIsNumeric($redStats['percentage']);
        $this->assertEquals(66.67, round((float) $redStats['percentage'], 2));
        $this->assertEquals(1, $blueStats['count']);
        $this->assertIsNumeric($blueStats['percentage']);
        $this->assertEquals(33.33, round((float) $blueStats['percentage'], 2));

        // Verify choice texts are included in statistics
        $this->assertArrayHasKey('selectedChoiceText', $redStats['answer']);
        $this->assertEquals('Red', $redStats['answer']['selectedChoiceText']);
        $this->assertArrayHasKey('selectedChoiceText', $blueStats['answer']);
        $this->assertEquals('Blue', $blueStats['answer']['selectedChoiceText']);

        // Verify myAnswer includes choice text (User 1 answered Red)
        $this->assertArrayHasKey('myAnswer', $question);
        $this->assertIsArray($question['myAnswer']);
        $this->assertArrayHasKey('selectedChoiceId', $question['myAnswer']);
        $this->assertEquals(CurrentChallenge2Fixture::CHOICE_20_ID, $question['myAnswer']['selectedChoiceId']);
        $this->assertArrayHasKey('selectedChoiceText', $question['myAnswer']);
        $this->assertEquals('Red', $question['myAnswer']['selectedChoiceText']);

        // Verify correct answer includes choice text
        $this->assertArrayHasKey('correctAnswer', $question);
        $this->assertIsArray($question['correctAnswer']);
        $this->assertArrayHasKey('selectedChoiceId', $question['correctAnswer']);
        $this->assertEquals(CurrentChallenge2Fixture::CHOICE_20_ID, $question['correctAnswer']['selectedChoiceId']);
        $this->assertArrayHasKey('selectedChoiceText', $question['correctAnswer']);
        $this->assertEquals('Red', $question['correctAnswer']['selectedChoiceText']);
    }

    public function testNoStatisticsWhenShowStatisticsContinuouslyIsFalse(): void
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

        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData['questions']);
        $this->assertCount(3, $responseData['questions']);

        // Verify statistics are NULL for unevaluated challenge with showStatisticsContinuously=false
        foreach ($responseData['questions'] as $question) {
            $this->assertNull($question['statistics']);
        }
    }

    public function testCorrectAnswerIsNullWhenNotProvided(): void
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

        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertIsArray($responseData['questions']);

        // Verify correctAnswer is NULL when not set on questions
        foreach ($responseData['questions'] as $question) {
            $this->assertIsArray($question);
            $this->assertArrayHasKey('correctAnswer', $question);
            $this->assertNull($question['correctAnswer']);
        }
    }
}
