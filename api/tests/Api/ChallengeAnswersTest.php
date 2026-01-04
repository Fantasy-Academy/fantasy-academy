<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Tests\DataFixtures\CurrentChallenge1Fixture;
use FantasyAcademy\API\Tests\DataFixtures\ExpiredChallenge2Fixture;
use FantasyAcademy\API\Tests\DataFixtures\ExpiredChallengeFixture;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;
use FantasyAcademy\API\Tests\TestingLogin;

/**
 * @covers \FantasyAcademy\API\Api\ChallengeAnswers\ChallengeAnswersProvider
 * @covers \FantasyAcademy\API\Api\ChallengeAnswers\ChallengeAnswersResponse
 */
final class ChallengeAnswersTest extends ApiTestCase
{
    public function testChallengeAnswersForEvaluatedChallenge(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_1_EMAIL);

        $response = $client->request('GET', '/api/challenges/' . ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID . '/answers', [
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
        $this->assertEquals(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID, $responseData['id']);
        $this->assertArrayHasKey('players', $responseData);
        $this->assertIsArray($responseData['players']);
        $this->assertCount(3, $responseData['players']); // 3 users answered this challenge

        $players = $responseData['players'];

        // Verify ordering by points DESC (User 2: 900, User 3: 900, User 1: 800)
        // First player (User 2 or User 3 with 900 points)
        $this->assertIsArray($players[0]);
        $this->assertEquals(900, $players[0]['points']);
        $this->assertArrayHasKey('userId', $players[0]);
        $this->assertArrayHasKey('userName', $players[0]);
        $this->assertArrayHasKey('isMyself', $players[0]);
        $this->assertArrayHasKey('questions', $players[0]);

        // Second player (User 3 or User 2 with 900 points)
        $this->assertEquals(900, $players[1]['points']);

        // Third player (User 1 with 800 points)
        $this->assertEquals(800, $players[2]['points']);
        $this->assertEquals(UserFixture::USER_1_ID, $players[2]['userId']);
        $this->assertEquals('User 1', $players[2]['userName']);
        $this->assertTrue($players[2]['isMyself']); // Authenticated as User 1

        // Verify questions structure for User 1
        $this->assertIsArray($players[2]['questions']);
        $this->assertCount(1, $players[2]['questions']); // ExpiredChallenge has 1 question

        $question = $players[2]['questions'][0];
        $this->assertIsArray($question);
        $this->assertArrayHasKey('questionId', $question);
        $this->assertEquals(ExpiredChallengeFixture::QUESTION_7_ID, $question['questionId']);
        $this->assertArrayHasKey('questionText', $question);
        $this->assertEquals('Some dummy expired question', $question['questionText']);
        $this->assertArrayHasKey('answer', $question);
        $this->assertIsArray($question['answer']);
        $this->assertArrayHasKey('textAnswer', $question['answer']);
        $this->assertEquals('User 1 answer to question 7', $question['answer']['textAnswer']);
    }

    public function testChallengeAnswersAsUnauthenticated(): void
    {
        $client = self::createClient();

        $response = $client->request('GET', '/api/challenges/' . ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID . '/answers', [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('players', $responseData);
        $this->assertIsArray($responseData['players']);
        $this->assertCount(3, $responseData['players']);

        // Verify all isMyself flags are false when unauthenticated
        foreach ($responseData['players'] as $player) {
            $this->assertFalse($player['isMyself']);
        }
    }

    public function testChallengeAnswersForUnevaluatedChallengeReturnsEmptyArray(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_3_EMAIL);

        $response = $client->request('GET', '/api/challenges/' . CurrentChallenge1Fixture::CURRENT_CHALLENGE_1_ID . '/answers', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals(CurrentChallenge1Fixture::CURRENT_CHALLENGE_1_ID, $responseData['id']);
        $this->assertArrayHasKey('players', $responseData);
        $this->assertIsArray($responseData['players']);
        $this->assertCount(0, $responseData['players']); // Empty players array for unevaluated challenge
    }

    public function testIsMyselfFlagCorrectForDifferentUsers(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_2_EMAIL);

        $response = $client->request('GET', '/api/challenges/' . ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID . '/answers', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();

        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('players', $responseData);
        $this->assertIsArray($responseData['players']);

        // Find User 2 in the results
        $user2Found = false;
        foreach ($responseData['players'] as $player) {
            if ($player['userId'] === UserFixture::USER_2_ID) {
                $this->assertTrue($player['isMyself']);
                $this->assertEquals('User 2', $player['userName']);
                $user2Found = true;
            } else {
                $this->assertFalse($player['isMyself']);
            }
        }

        $this->assertTrue($user2Found, 'User 2 should be in the results');
    }

    public function testChallengeAnswersIncludesChoiceTexts(): void
    {
        $client = self::createClient();
        $token = TestingLogin::getJwt($client, UserFixture::USER_1_EMAIL);

        $response = $client->request('GET', '/api/challenges/' . ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID . '/answers', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertEquals(ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID, $responseData['id']);
        $this->assertArrayHasKey('players', $responseData);
        $this->assertIsArray($responseData['players']);
        $this->assertCount(3, $responseData['players']); // 3 users answered this challenge

        // Find User 1's answers (should be first with 1000 points - all correct)
        $user1Data = null;
        foreach ($responseData['players'] as $player) {
            if ($player['userId'] === UserFixture::USER_1_ID) {
                $user1Data = $player;
                break;
            }
        }

        $this->assertNotNull($user1Data, 'User 1 should be in results');
        $this->assertEquals(1000, $user1Data['points']);
        $this->assertIsArray($user1Data['questions']);
        $this->assertCount(3, $user1Data['questions']); // 3 questions in ExpiredChallenge2

        // Find specific questions
        $question8 = null;
        $question9 = null;
        foreach ($user1Data['questions'] as $question) {
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

        // Question 9 (MultiSelect) - User 1 selected 7 and 13 (CHOICE_12_ID, CHOICE_13_ID)
        $this->assertIsArray($question9['answer']['selectedChoiceIds']);
        $this->assertCount(2, $question9['answer']['selectedChoiceIds']);
        $this->assertEquals(ExpiredChallenge2Fixture::CHOICE_12_ID, $question9['answer']['selectedChoiceIds'][0]);
        $this->assertEquals(ExpiredChallenge2Fixture::CHOICE_13_ID, $question9['answer']['selectedChoiceIds'][1]);
        $this->assertIsArray($question9['answer']['selectedChoiceTexts']);
        $this->assertCount(2, $question9['answer']['selectedChoiceTexts']);
        $this->assertEquals('7', $question9['answer']['selectedChoiceTexts'][0]);
        $this->assertEquals('13', $question9['answer']['selectedChoiceTexts'][1]);

        // Find User 2's answers to verify different selections
        /** @var null|array{userId: string, questions: array<mixed>} $user2Data */
        $user2Data = null;
        foreach ($responseData['players'] as $player) {
            if ($player['userId'] === UserFixture::USER_2_ID) {
                $user2Data = $player;
                break;
            }
        }

        $this->assertNotNull($user2Data, 'User 2 should be in results');
        $this->assertIsArray($user2Data['questions']);

        // Find User 2's question 8 answer
        $user2Question8 = null;
        $user2Question9 = null;
        foreach ($user2Data['questions'] as $question) {
            if ($question['questionId'] === ExpiredChallenge2Fixture::QUESTION_8_ID) {
                $user2Question8 = $question;
            } elseif ($question['questionId'] === ExpiredChallenge2Fixture::QUESTION_9_ID) {
                $user2Question9 = $question;
            }
        }

        // User 2 selected Blue (CHOICE_10_ID) for question 8
        $this->assertEquals(ExpiredChallenge2Fixture::CHOICE_10_ID, $user2Question8['answer']['selectedChoiceId']);
        $this->assertEquals('Blue', $user2Question8['answer']['selectedChoiceText']);

        // User 2 selected only 7 (CHOICE_12_ID) for question 9
        $this->assertIsArray($user2Question9['answer']['selectedChoiceIds']);
        $this->assertCount(1, $user2Question9['answer']['selectedChoiceIds']);
        $this->assertEquals(ExpiredChallenge2Fixture::CHOICE_12_ID, $user2Question9['answer']['selectedChoiceIds'][0]);
        $this->assertIsArray($user2Question9['answer']['selectedChoiceTexts']);
        $this->assertCount(1, $user2Question9['answer']['selectedChoiceTexts']);
        $this->assertEquals('7', $user2Question9['answer']['selectedChoiceTexts'][0]);
    }
}
