<?php
declare(strict_types=1);

namespace FantasyAcademy\API\Tests\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use FantasyAcademy\API\Entity\Challenge;
use FantasyAcademy\API\Entity\PlayerChallengeAnswer;
use FantasyAcademy\API\Entity\Question;
use FantasyAcademy\API\Entity\User;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

final class PlayerChallengeAnswerFixture extends Fixture implements DependentFixtureInterface
{
    public const string USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID = '00000000-0000-0000-0005-000000000001';
    public const string USER_1_EXPIRED_CHALLENGE_2_ANSWER_ID = '00000000-0000-0000-0005-000000000002';
    public const string USER_2_EXPIRED_CHALLENGE_1_ANSWER_ID = '00000000-0000-0000-0005-000000000003';
    public const string USER_2_EXPIRED_CHALLENGE_2_ANSWER_ID = '00000000-0000-0000-0005-000000000004';
    public const string USER_3_EXPIRED_CHALLENGE_1_ANSWER_ID = '00000000-0000-0000-0005-000000000005';
    public const string USER_3_EXPIRED_CHALLENGE_2_ANSWER_ID = '00000000-0000-0000-0005-000000000006';
    public const string USER_3_CURRENT_CHALLENGE_1_ANSWER_ID = '00000000-0000-0000-0005-000000000007';
    public const string USER_1_EXPIRED_CHALLENGE_3_ANSWER_ID = '00000000-0000-0000-0005-000000000008';
    public const string USER_1_CURRENT_CHALLENGE_2_ANSWER_ID = '00000000-0000-0000-0005-000000000009';
    public const string USER_2_CURRENT_CHALLENGE_2_ANSWER_ID = '00000000-0000-0000-0005-000000000010';
    public const string USER_3_CURRENT_CHALLENGE_2_ANSWER_ID = '00000000-0000-0000-0005-000000000011';

    // New challenge answers - ExpiredChallenge4
    public const string USER_1_EXPIRED_CHALLENGE_4_ANSWER_ID = '00000000-0000-0000-0005-000000000012';
    public const string USER_2_EXPIRED_CHALLENGE_4_ANSWER_ID = '00000000-0000-0000-0005-000000000013';
    public const string USER_3_EXPIRED_CHALLENGE_4_ANSWER_ID = '00000000-0000-0000-0005-000000000014';
    public const string USER_4_EXPIRED_CHALLENGE_4_ANSWER_ID = '00000000-0000-0000-0005-000000000015';

    // ExpiredChallenge5
    public const string USER_1_EXPIRED_CHALLENGE_5_ANSWER_ID = '00000000-0000-0000-0005-000000000016';
    public const string USER_2_EXPIRED_CHALLENGE_5_ANSWER_ID = '00000000-0000-0000-0005-000000000017';
    public const string USER_3_EXPIRED_CHALLENGE_5_ANSWER_ID = '00000000-0000-0000-0005-000000000018';
    public const string USER_4_EXPIRED_CHALLENGE_5_ANSWER_ID = '00000000-0000-0000-0005-000000000019';

    // ExpiredChallenge6
    public const string USER_1_EXPIRED_CHALLENGE_6_ANSWER_ID = '00000000-0000-0000-0005-000000000020';
    public const string USER_2_EXPIRED_CHALLENGE_6_ANSWER_ID = '00000000-0000-0000-0005-000000000021';
    public const string USER_3_EXPIRED_CHALLENGE_6_ANSWER_ID = '00000000-0000-0000-0005-000000000022';
    public const string USER_4_EXPIRED_CHALLENGE_6_ANSWER_ID = '00000000-0000-0000-0005-000000000023';

    // ExpiredChallenge7
    public const string USER_1_EXPIRED_CHALLENGE_7_ANSWER_ID = '00000000-0000-0000-0005-000000000024';
    public const string USER_2_EXPIRED_CHALLENGE_7_ANSWER_ID = '00000000-0000-0000-0005-000000000025';
    public const string USER_3_EXPIRED_CHALLENGE_7_ANSWER_ID = '00000000-0000-0000-0005-000000000026';
    public const string USER_4_EXPIRED_CHALLENGE_7_ANSWER_ID = '00000000-0000-0000-0005-000000000027';

    public function __construct(
        readonly private ClockInterface $clock,
    ) {
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            ExpiredChallengeFixture::class,
            ExpiredChallenge2Fixture::class,
            ExpiredChallenge3Fixture::class,
            ExpiredChallenge4Fixture::class,
            ExpiredChallenge5Fixture::class,
            ExpiredChallenge6Fixture::class,
            ExpiredChallenge7Fixture::class,
            CurrentChallenge1Fixture::class,
            CurrentChallenge2Fixture::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $answeredAt = $this->clock->now()->modify('-2 days');

        // Load users
        $user1 = $manager->find(User::class, Uuid::fromString(UserFixture::USER_1_ID));
        assert($user1 !== null);
        $user2 = $manager->find(User::class, Uuid::fromString(UserFixture::USER_2_ID));
        assert($user2 !== null);
        $user3 = $manager->find(User::class, Uuid::fromString(UserFixture::USER_3_ID));
        assert($user3 !== null);
        $user4 = $manager->find(User::class, Uuid::fromString(UserFixture::USER_4_ID));
        assert($user4 !== null);

        // Load challenges
        $expiredChallenge1 = $manager->find(Challenge::class, Uuid::fromString(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID));
        assert($expiredChallenge1 !== null);
        $expiredChallenge2 = $manager->find(Challenge::class, Uuid::fromString(ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID));
        assert($expiredChallenge2 !== null);
        $expiredChallenge3 = $manager->find(Challenge::class, Uuid::fromString(ExpiredChallenge3Fixture::EXPIRED_CHALLENGE_3_ID));
        assert($expiredChallenge3 !== null);
        $expiredChallenge4 = $manager->find(Challenge::class, Uuid::fromString(ExpiredChallenge4Fixture::EXPIRED_CHALLENGE_4_ID));
        assert($expiredChallenge4 !== null);
        $expiredChallenge5 = $manager->find(Challenge::class, Uuid::fromString(ExpiredChallenge5Fixture::EXPIRED_CHALLENGE_5_ID));
        assert($expiredChallenge5 !== null);
        $expiredChallenge6 = $manager->find(Challenge::class, Uuid::fromString(ExpiredChallenge6Fixture::EXPIRED_CHALLENGE_6_ID));
        assert($expiredChallenge6 !== null);
        $expiredChallenge7 = $manager->find(Challenge::class, Uuid::fromString(ExpiredChallenge7Fixture::EXPIRED_CHALLENGE_7_ID));
        assert($expiredChallenge7 !== null);
        $currentChallenge1 = $manager->find(Challenge::class, Uuid::fromString(CurrentChallenge1Fixture::CURRENT_CHALLENGE_1_ID));
        assert($currentChallenge1 !== null);
        $currentChallenge2 = $manager->find(Challenge::class, Uuid::fromString(CurrentChallenge2Fixture::CURRENT_CHALLENGE_2_ID));
        assert($currentChallenge2 !== null);

        // Load questions
        $question7 = $manager->find(Question::class, Uuid::fromString(ExpiredChallengeFixture::QUESTION_7_ID));
        assert($question7 !== null);
        $question8 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge2Fixture::QUESTION_8_ID));
        assert($question8 !== null);
        $question9 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge2Fixture::QUESTION_9_ID));
        assert($question9 !== null);
        $question10 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge2Fixture::QUESTION_10_ID));
        assert($question10 !== null);
        $question11 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge3Fixture::QUESTION_11_ID));
        assert($question11 !== null);
        $question1 = $manager->find(Question::class, Uuid::fromString(CurrentChallenge1Fixture::QUESTION_1_ID));
        assert($question1 !== null);
        $question2 = $manager->find(Question::class, Uuid::fromString(CurrentChallenge1Fixture::QUESTION_2_ID));
        assert($question2 !== null);
        $question3 = $manager->find(Question::class, Uuid::fromString(CurrentChallenge1Fixture::QUESTION_3_ID));
        assert($question3 !== null);
        $question4 = $manager->find(Question::class, Uuid::fromString(CurrentChallenge2Fixture::QUESTION_4_ID));
        assert($question4 !== null);

        // ExpiredChallenge4 questions
        $question12 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge4Fixture::QUESTION_12_ID));
        assert($question12 !== null);
        $question13 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge4Fixture::QUESTION_13_ID));
        assert($question13 !== null);
        $question14 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge4Fixture::QUESTION_14_ID));
        assert($question14 !== null);
        $question15 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge4Fixture::QUESTION_15_ID));
        assert($question15 !== null);

        // ExpiredChallenge5 questions
        $question16 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge5Fixture::QUESTION_16_ID));
        assert($question16 !== null);
        $question17 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge5Fixture::QUESTION_17_ID));
        assert($question17 !== null);
        $question18 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge5Fixture::QUESTION_18_ID));
        assert($question18 !== null);

        // ExpiredChallenge6 questions
        $question19 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge6Fixture::QUESTION_19_ID));
        assert($question19 !== null);
        $question20 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge6Fixture::QUESTION_20_ID));
        assert($question20 !== null);
        $question21 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge6Fixture::QUESTION_21_ID));
        assert($question21 !== null);

        // ExpiredChallenge7 questions
        $question22 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge7Fixture::QUESTION_22_ID));
        assert($question22 !== null);
        $question23 = $manager->find(Question::class, Uuid::fromString(ExpiredChallenge7Fixture::QUESTION_23_ID));
        assert($question23 !== null);

        // User 1 (admin@example.com) - answers all expired challenges
        $this->createAnswersForUser1($manager, $user1, $expiredChallenge1, $expiredChallenge2, $expiredChallenge3, $currentChallenge2, $answeredAt, $question7, $question8, $question9, $question10, $question11, $question4);

        // User 2 (user@example.com) - answers both expired challenges
        $this->createAnswersForUser2($manager, $user2, $expiredChallenge1, $expiredChallenge2, $currentChallenge2, $answeredAt, $question7, $question8, $question9, $question10, $question4);

        // User 3 (user3@example.com) - answers both expired challenges + 1 current challenge
        $this->createAnswersForUser3($manager, $user3, $expiredChallenge1, $expiredChallenge2, $currentChallenge1, $currentChallenge2, $answeredAt, $question7, $question8, $question9, $question10, $question1, $question2, $question3, $question4);

        // ========================================
        // NEW CHALLENGES - answers for all users
        // Each challenge needs answers before its expiresAt date
        // ========================================

        // ExpiredChallenge4: expiresAt = now - 4 days, so answer at now - 5 days
        // USER_4 does NOT answer this challenge (per plan: USER_4 only answers Challenge6 and Challenge7)
        $answeredAtChallenge4 = $this->clock->now()->modify('-5 days');
        $this->createAnswersForChallenge4($manager, $user1, $user2, $user3, $expiredChallenge4, $answeredAtChallenge4, $question12, $question13, $question14, $question15);

        // ExpiredChallenge5: expiresAt = now - 3 weeks, so answer at now - 4 weeks
        // USER_4 does NOT answer this challenge (per plan: USER_4 only answers Challenge6 and Challenge7)
        $answeredAtChallenge5 = $this->clock->now()->modify('-4 weeks');
        $this->createAnswersForChallenge5($manager, $user1, $user2, $user3, $expiredChallenge5, $answeredAtChallenge5, $question16, $question17, $question18);

        // ExpiredChallenge6: expiresAt = now - 8 days, so answer at now - 9 days
        $answeredAtChallenge6 = $this->clock->now()->modify('-9 days');
        $this->createAnswersForChallenge6($manager, $user1, $user2, $user3, $user4, $expiredChallenge6, $answeredAtChallenge6, $question19, $question20, $question21);

        // ExpiredChallenge7: expiresAt = now - 5 days, so answer at now - 6 days
        $answeredAtChallenge7 = $this->clock->now()->modify('-6 days');
        $this->createAnswersForChallenge7($manager, $user1, $user2, $user3, $user4, $expiredChallenge7, $answeredAtChallenge7, $question22, $question23);

        // Evaluate challenges at different times to test weekly change
        // Challenge 1 evaluated 2 weeks ago (before last Monday) - will count in "previous week" stats
        $expiredChallenge1->evaluate($this->clock->now()->modify('-2 weeks'));
        // Challenge 2 evaluated 2 days ago (after last Monday) - will count in "current week" stats
        $expiredChallenge2->evaluate($this->clock->now()->modify('-2 days'));
        // Challenge 3 will remain unevaluated to test import evaluation

        // New challenges evaluations
        // Note: "current week" = after last Monday, "previous week" = before last Monday
        $expiredChallenge4->evaluate($this->clock->now()->modify('-3 days')); // GW2, current week
        $expiredChallenge5->evaluate($this->clock->now()->modify('-2 weeks')); // GW1, previous week
        $expiredChallenge6->evaluate($this->clock->now()->modify('-5 days')); // GW3, current week (changed from -1 week to be clearly in current week)
        $expiredChallenge7->evaluate($this->clock->now()->modify('-4 days')); // GW2, current week

        $manager->flush();
    }

    private function createAnswersForUser1(
        ObjectManager $manager,
        User $user,
        Challenge $expiredChallenge1,
        Challenge $expiredChallenge2,
        Challenge $expiredChallenge3,
        Challenge $currentChallenge2,
        \DateTimeImmutable $answeredAt,
        Question $question7,
        Question $question8,
        Question $question9,
        Question $question10,
        Question $question11,
        Question $question4,
    ): void
    {
        // Answer expired challenge 1
        $answer1 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID),
            challenge: $expiredChallenge1,
            user: $user,
        );

        $answer1->answerQuestion(
            $answeredAt,
            $question7,
            textAnswer: 'User 1 answer to question 7',
            numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: null,
            orderedChoiceIds: null,
        );

        $answer1->evaluate(800);
        $manager->persist($answer1);

        // Answer expired challenge 2
        $answer2 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_1_EXPIRED_CHALLENGE_2_ANSWER_ID),
            challenge: $expiredChallenge2,
            user: $user,
        );

        $answer2->answerQuestion(
            $answeredAt,
            $question8,
            textAnswer: null,
            numericAnswer: null,
            selectedChoiceId: Uuid::fromString(ExpiredChallenge2Fixture::CHOICE_9_ID),
            selectedChoiceIds: null,
            orderedChoiceIds: null,
        );

        $answer2->answerQuestion(
            $answeredAt,
            $question9,
            textAnswer: null,
            numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: [
                Uuid::fromString(ExpiredChallenge2Fixture::CHOICE_12_ID),
                Uuid::fromString(ExpiredChallenge2Fixture::CHOICE_13_ID),
            ],
            orderedChoiceIds: null,
        );

        $answer2->answerQuestion(
            $answeredAt,
            $question10,
            textAnswer: null,
            numericAnswer: 42.0,
            selectedChoiceId: null,
            selectedChoiceIds: null,
            orderedChoiceIds: null,
        );

        $answer2->evaluate(1000); // All correct answers - gets max points

        $manager->persist($answer2);

        // Answer expired challenge 3 (will NOT be evaluated initially)
        $answer3 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_1_EXPIRED_CHALLENGE_3_ANSWER_ID),
            challenge: $expiredChallenge3,
            user: $user,
        );

        $answer3->answerQuestion(
            $answeredAt,
            $question11,
            textAnswer: 'User 1 answer to unevaluated challenge',
            numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: null,
            orderedChoiceIds: null,
        );

        $answer3->evaluate(500);

        $manager->persist($answer3);

        // Answer current challenge 2 (with showStatisticsContinuously=true)
        $currentAnsweredAt = $this->clock->now()->modify('-1 day');
        $answer4 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_1_CURRENT_CHALLENGE_2_ANSWER_ID),
            challenge: $currentChallenge2,
            user: $user,
        );

        $answer4->answerQuestion(
            $currentAnsweredAt,
            $question4,
            textAnswer: null,
            numericAnswer: null,
            selectedChoiceId: Uuid::fromString(CurrentChallenge2Fixture::CHOICE_20_ID), // Red
            selectedChoiceIds: null,
            orderedChoiceIds: null,
        );

        $manager->persist($answer4);
    }

    private function createAnswersForUser2(
        ObjectManager $manager,
        User $user,
        Challenge $expiredChallenge1,
        Challenge $expiredChallenge2,
        Challenge $currentChallenge2,
        \DateTimeImmutable $answeredAt,
        Question $question7,
        Question $question8,
        Question $question9,
        Question $question10,
        Question $question4,
    ): void
    {
        // Answer expired challenge 1
        $answer1 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_2_EXPIRED_CHALLENGE_1_ANSWER_ID),
            challenge: $expiredChallenge1,
            user: $user,
        );

        $answer1->answerQuestion(
            $answeredAt,
            $question7,
            textAnswer: 'User 2 answer to question 7',
            numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: null,
            orderedChoiceIds: null,
        );

        $answer1->evaluate(900);

        $manager->persist($answer1);

        // Answer expired challenge 2
        $answer2 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_2_EXPIRED_CHALLENGE_2_ANSWER_ID),
            challenge: $expiredChallenge2,
            user: $user,
        );

        $answer2->answerQuestion(
            $answeredAt,
            $question8,
            textAnswer: null,
            numericAnswer: null,
            selectedChoiceId: Uuid::fromString(ExpiredChallenge2Fixture::CHOICE_10_ID),
            selectedChoiceIds: null,
            orderedChoiceIds: null,
        );

        $answer2->answerQuestion(
            $answeredAt,
            $question9,
            textAnswer: null,
            numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: [
                Uuid::fromString(ExpiredChallenge2Fixture::CHOICE_12_ID),
            ],
            orderedChoiceIds: null,
        );

        $answer2->answerQuestion(
            $answeredAt,
            $question10,
            textAnswer: null,
            numericAnswer: 100.0,
            selectedChoiceId: null,
            selectedChoiceIds: null,
            orderedChoiceIds: null,
        );

        $answer2->evaluate(400); // All wrong answers - gets low points

        $manager->persist($answer2);

        // Answer current challenge 2 (with showStatisticsContinuously=true)
        $currentAnsweredAt = $this->clock->now()->modify('-1 day');
        $answer3 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_2_CURRENT_CHALLENGE_2_ANSWER_ID),
            challenge: $currentChallenge2,
            user: $user,
        );

        $answer3->answerQuestion(
            $currentAnsweredAt,
            $question4,
            textAnswer: null,
            numericAnswer: null,
            selectedChoiceId: Uuid::fromString(CurrentChallenge2Fixture::CHOICE_21_ID), // Blue
            selectedChoiceIds: null,
            orderedChoiceIds: null,
        );

        $manager->persist($answer3);
    }

    private function createAnswersForUser3(
        ObjectManager $manager,
        User $user,
        Challenge $expiredChallenge1,
        Challenge $expiredChallenge2,
        Challenge $currentChallenge1,
        Challenge $currentChallenge2,
        \DateTimeImmutable $answeredAt,
        Question $question7,
        Question $question8,
        Question $question9,
        Question $question10,
        Question $question1,
        Question $question2,
        Question $question3,
        Question $question4,
    ): void
    {
        // Answer expired challenge 1
        $answer1 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_3_EXPIRED_CHALLENGE_1_ANSWER_ID),
            challenge: $expiredChallenge1,
            user: $user,
        );

        $answer1->answerQuestion(
            $answeredAt,
            $question7,
            textAnswer: 'User 3 answer to question 7',
            numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: null,
            orderedChoiceIds: null,
        );

        $answer1->evaluate(900);

        $manager->persist($answer1);

        // Answer expired challenge 2
        $answer2 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_3_EXPIRED_CHALLENGE_2_ANSWER_ID),
            challenge: $expiredChallenge2,
            user: $user,
        );

        $answer2->answerQuestion(
            $answeredAt,
            $question8,
            textAnswer: null,
            numericAnswer: null,
            selectedChoiceId: Uuid::fromString(ExpiredChallenge2Fixture::CHOICE_11_ID),
            selectedChoiceIds: null,
            orderedChoiceIds: null,
        );

        $answer2->answerQuestion(
            $answeredAt,
            $question9,
            textAnswer: null,
            numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: [
                Uuid::fromString(ExpiredChallenge2Fixture::CHOICE_13_ID),
            ],
            orderedChoiceIds: null,
        );


        $answer2->answerQuestion(
            $answeredAt,
            $question10,
            textAnswer: null,
            numericAnswer: 50.0,
            selectedChoiceId: null,
            selectedChoiceIds: null,
            orderedChoiceIds: null,
        );

        $answer2->evaluate(200); // All wrong answers - gets lowest points

        $manager->persist($answer2);

        // Answer current challenge 1 (use current time since it's not expired)
        $currentAnsweredAt = $this->clock->now()->modify('-1 day');
        $answer3 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_3_CURRENT_CHALLENGE_1_ANSWER_ID),
            challenge: $currentChallenge1,
            user: $user,
        );

        $answer3->answerQuestion(
            $currentAnsweredAt,
            $question1,
            textAnswer: null,
            numericAnswer: null,
            selectedChoiceId: Uuid::fromString(CurrentChallenge1Fixture::CHOICE_1_ID),
            selectedChoiceIds: null,
            orderedChoiceIds: null,
        );

        $answer3->answerQuestion(
            $currentAnsweredAt,
            $question2,
            textAnswer: null,
            numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: [
                Uuid::fromString(CurrentChallenge1Fixture::CHOICE_3_ID),
                Uuid::fromString(CurrentChallenge1Fixture::CHOICE_5_ID),
            ],
            orderedChoiceIds: null,
        );

        $answer3->answerQuestion(
            $currentAnsweredAt,
            $question3,
            textAnswer: null,
            numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: null,
            orderedChoiceIds: [
                Uuid::fromString(CurrentChallenge1Fixture::CHOICE_6_ID),
                Uuid::fromString(CurrentChallenge1Fixture::CHOICE_7_ID),
                Uuid::fromString(CurrentChallenge1Fixture::CHOICE_8_ID),
            ],
        );

        $manager->persist($answer3);

        // Answer current challenge 2 (with showStatisticsContinuously=true)
        $answer4 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_3_CURRENT_CHALLENGE_2_ANSWER_ID),
            challenge: $currentChallenge2,
            user: $user,
        );

        $answer4->answerQuestion(
            $currentAnsweredAt,
            $question4,
            textAnswer: null,
            numericAnswer: null,
            selectedChoiceId: Uuid::fromString(CurrentChallenge2Fixture::CHOICE_20_ID), // Red
            selectedChoiceIds: null,
            orderedChoiceIds: null,
        );

        $manager->persist($answer4);
    }

    /**
     * ExpiredChallenge4 - PL Predictions (4 questions)
     * Q12: SingleSelect (Who wins PL?) - Correct: Man City (CHOICE_17)
     * Q13: MultiSelect (Top 3 teams) - Correct: Arsenal, Man City, Liverpool (CHOICE_21, 22, 23)
     * Q14: Numeric (Top scorer goals) - Correct: 36.0
     * Q15: Text (Top scorer name) - Correct: "Haaland"
     */
    private function createAnswersForChallenge4(
        ObjectManager $manager,
        User $user1,
        User $user2,
        User $user3,
        Challenge $challenge,
        \DateTimeImmutable $answeredAt,
        Question $question12,
        Question $question13,
        Question $question14,
        Question $question15,
    ): void {
        // USER_1: 4/4 correct = 1000 points
        $answer1 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_1_EXPIRED_CHALLENGE_4_ANSWER_ID),
            challenge: $challenge,
            user: $user1,
        );
        $answer1->answerQuestion($answeredAt, $question12, textAnswer: null, numericAnswer: null,
            selectedChoiceId: Uuid::fromString(ExpiredChallenge4Fixture::CHOICE_15_ID), // Man City (correct)
            selectedChoiceIds: null, orderedChoiceIds: null);
        $answer1->answerQuestion($answeredAt, $question13, textAnswer: null, numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: [
                Uuid::fromString(ExpiredChallenge4Fixture::CHOICE_17_ID), // Arsenal
                Uuid::fromString(ExpiredChallenge4Fixture::CHOICE_18_ID), // Man City
                Uuid::fromString(ExpiredChallenge4Fixture::CHOICE_19_ID), // Liverpool
            ], orderedChoiceIds: null);
        $answer1->answerQuestion($answeredAt, $question14, textAnswer: null, numericAnswer: 27.0, // correct
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer1->answerQuestion($answeredAt, $question15, textAnswer: 'Haaland', numericAnswer: null, // correct
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer1->evaluate(1000);
        $manager->persist($answer1);

        // USER_2: 2/4 correct = 500 points (Q12 wrong, Q13 wrong, Q14 correct, Q15 correct)
        $answer2 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_2_EXPIRED_CHALLENGE_4_ANSWER_ID),
            challenge: $challenge,
            user: $user2,
        );
        $answer2->answerQuestion($answeredAt, $question12, textAnswer: null, numericAnswer: null,
            selectedChoiceId: Uuid::fromString(ExpiredChallenge4Fixture::CHOICE_14_ID), // Arsenal (wrong)
            selectedChoiceIds: null, orderedChoiceIds: null);
        $answer2->answerQuestion($answeredAt, $question13, textAnswer: null, numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: [
                Uuid::fromString(ExpiredChallenge4Fixture::CHOICE_17_ID), // Arsenal (correct)
                Uuid::fromString(ExpiredChallenge4Fixture::CHOICE_19_ID), // Liverpool (correct)
                Uuid::fromString(ExpiredChallenge4Fixture::CHOICE_22_ID), // Chelsea (wrong)
            ], orderedChoiceIds: null);
        $answer2->answerQuestion($answeredAt, $question14, textAnswer: null, numericAnswer: 27.0, // correct
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer2->answerQuestion($answeredAt, $question15, textAnswer: 'Haaland', numericAnswer: null, // correct
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer2->evaluate(500);
        $manager->persist($answer2);

        // USER_3: 1/4 correct = 250 points (only Q15 correct)
        $answer3 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_3_EXPIRED_CHALLENGE_4_ANSWER_ID),
            challenge: $challenge,
            user: $user3,
        );
        $answer3->answerQuestion($answeredAt, $question12, textAnswer: null, numericAnswer: null,
            selectedChoiceId: Uuid::fromString(ExpiredChallenge4Fixture::CHOICE_16_ID), // Liverpool (wrong)
            selectedChoiceIds: null, orderedChoiceIds: null);
        $answer3->answerQuestion($answeredAt, $question13, textAnswer: null, numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: [
                Uuid::fromString(ExpiredChallenge4Fixture::CHOICE_18_ID), // Man City (correct)
                Uuid::fromString(ExpiredChallenge4Fixture::CHOICE_19_ID), // Liverpool (correct)
                Uuid::fromString(ExpiredChallenge4Fixture::CHOICE_22_ID), // Chelsea (wrong)
            ], orderedChoiceIds: null);
        $answer3->answerQuestion($answeredAt, $question14, textAnswer: null, numericAnswer: 25.0, // wrong
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer3->answerQuestion($answeredAt, $question15, textAnswer: 'Haaland', numericAnswer: null, // correct
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer3->evaluate(250);
        $manager->persist($answer3);

        // Note: USER_4 does NOT answer this challenge (per plan: USER_4 only answers Challenge6 and Challenge7)
    }

    /**
     * ExpiredChallenge5 - Player Rankings (3 questions including Sort)
     * Q16: Sort (Rank players by goals) - Correct: Haaland, Salah, Son, Saka (CHOICE_23, 24, 25, 26)
     * Q17: SingleSelect (Best goalkeeper) - Correct: Alisson (CHOICE_27)
     * Q18: Numeric (Clean sheets) - Correct: 15.0
     */
    private function createAnswersForChallenge5(
        ObjectManager $manager,
        User $user1,
        User $user2,
        User $user3,
        Challenge $challenge,
        \DateTimeImmutable $answeredAt,
        Question $question16,
        Question $question17,
        Question $question18,
    ): void {
        // USER_1: 3/3 correct = 1000 points
        $answer1 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_1_EXPIRED_CHALLENGE_5_ANSWER_ID),
            challenge: $challenge,
            user: $user1,
        );
        $answer1->answerQuestion($answeredAt, $question16, textAnswer: null, numericAnswer: null,
            selectedChoiceId: null, selectedChoiceIds: null,
            orderedChoiceIds: [
                Uuid::fromString(ExpiredChallenge5Fixture::CHOICE_23_ID), // Haaland
                Uuid::fromString(ExpiredChallenge5Fixture::CHOICE_24_ID), // Salah
                Uuid::fromString(ExpiredChallenge5Fixture::CHOICE_25_ID), // Son
                Uuid::fromString(ExpiredChallenge5Fixture::CHOICE_26_ID), // Saka
            ]);
        $answer1->answerQuestion($answeredAt, $question17, textAnswer: null, numericAnswer: null,
            selectedChoiceId: Uuid::fromString(ExpiredChallenge5Fixture::CHOICE_27_ID), // Alisson (correct)
            selectedChoiceIds: null, orderedChoiceIds: null);
        $answer1->answerQuestion($answeredAt, $question18, textAnswer: null, numericAnswer: 15.0, // correct
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer1->evaluate(1000);
        $manager->persist($answer1);

        // USER_2: 2/3 correct = 667 points (Q16 wrong order, Q17 correct, Q18 correct)
        $answer2 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_2_EXPIRED_CHALLENGE_5_ANSWER_ID),
            challenge: $challenge,
            user: $user2,
        );
        $answer2->answerQuestion($answeredAt, $question16, textAnswer: null, numericAnswer: null,
            selectedChoiceId: null, selectedChoiceIds: null,
            orderedChoiceIds: [
                Uuid::fromString(ExpiredChallenge5Fixture::CHOICE_24_ID), // Salah (wrong order)
                Uuid::fromString(ExpiredChallenge5Fixture::CHOICE_23_ID), // Haaland
                Uuid::fromString(ExpiredChallenge5Fixture::CHOICE_25_ID), // Son
                Uuid::fromString(ExpiredChallenge5Fixture::CHOICE_26_ID), // Saka
            ]);
        $answer2->answerQuestion($answeredAt, $question17, textAnswer: null, numericAnswer: null,
            selectedChoiceId: Uuid::fromString(ExpiredChallenge5Fixture::CHOICE_27_ID), // Alisson (correct)
            selectedChoiceIds: null, orderedChoiceIds: null);
        $answer2->answerQuestion($answeredAt, $question18, textAnswer: null, numericAnswer: 15.0, // correct
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer2->evaluate(667);
        $manager->persist($answer2);

        // USER_3: 1/3 correct = 333 points (only Q18 correct)
        $answer3 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_3_EXPIRED_CHALLENGE_5_ANSWER_ID),
            challenge: $challenge,
            user: $user3,
        );
        $answer3->answerQuestion($answeredAt, $question16, textAnswer: null, numericAnswer: null,
            selectedChoiceId: null, selectedChoiceIds: null,
            orderedChoiceIds: [
                Uuid::fromString(ExpiredChallenge5Fixture::CHOICE_26_ID), // Saka (wrong order)
                Uuid::fromString(ExpiredChallenge5Fixture::CHOICE_25_ID), // Son
                Uuid::fromString(ExpiredChallenge5Fixture::CHOICE_24_ID), // Salah
                Uuid::fromString(ExpiredChallenge5Fixture::CHOICE_23_ID), // Haaland
            ]);
        $answer3->answerQuestion($answeredAt, $question17, textAnswer: null, numericAnswer: null,
            selectedChoiceId: Uuid::fromString(ExpiredChallenge5Fixture::CHOICE_28_ID), // Ederson (wrong)
            selectedChoiceIds: null, orderedChoiceIds: null);
        $answer3->answerQuestion($answeredAt, $question18, textAnswer: null, numericAnswer: 15.0, // correct
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer3->evaluate(333);
        $manager->persist($answer3);

        // Note: USER_4 does NOT answer this challenge (per plan: USER_4 only answers Challenge6 and Challenge7)
    }

    /**
     * ExpiredChallenge6 - Season Finale (3 questions)
     * Q19: MultiSelect (Relegated teams) - Correct: Luton, Burnley, Sheffield Utd (CHOICE_30, 31, 32)
     * Q20: Text (PL champion) - Correct: "Manchester City"
     * Q21: Sort (League position) - Correct: Man City, Arsenal, Liverpool, Villa (CHOICE_34, 35, 36, 37)
     */
    private function createAnswersForChallenge6(
        ObjectManager $manager,
        User $user1,
        User $user2,
        User $user3,
        User $user4,
        Challenge $challenge,
        \DateTimeImmutable $answeredAt,
        Question $question19,
        Question $question20,
        Question $question21,
    ): void {
        // USER_1: 3/3 correct = 1000 points
        $answer1 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_1_EXPIRED_CHALLENGE_6_ANSWER_ID),
            challenge: $challenge,
            user: $user1,
        );
        $answer1->answerQuestion($answeredAt, $question19, textAnswer: null, numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: [
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_30_ID), // Luton
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_31_ID), // Burnley
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_32_ID), // Sheffield Utd
            ], orderedChoiceIds: null);
        $answer1->answerQuestion($answeredAt, $question20, textAnswer: 'Manchester City', numericAnswer: null,
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer1->answerQuestion($answeredAt, $question21, textAnswer: null, numericAnswer: null,
            selectedChoiceId: null, selectedChoiceIds: null,
            orderedChoiceIds: [
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_34_ID), // Man City
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_35_ID), // Arsenal
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_36_ID), // Liverpool
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_37_ID), // Villa
            ]);
        $answer1->evaluate(1000);
        $manager->persist($answer1);

        // USER_2: 1/3 correct = 333 points (only Q20 correct)
        $answer2 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_2_EXPIRED_CHALLENGE_6_ANSWER_ID),
            challenge: $challenge,
            user: $user2,
        );
        $answer2->answerQuestion($answeredAt, $question19, textAnswer: null, numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: [
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_30_ID), // Luton (correct)
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_31_ID), // Burnley (correct)
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_33_ID), // Nottingham Forest (wrong)
            ], orderedChoiceIds: null);
        $answer2->answerQuestion($answeredAt, $question20, textAnswer: 'Manchester City', numericAnswer: null,
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer2->answerQuestion($answeredAt, $question21, textAnswer: null, numericAnswer: null,
            selectedChoiceId: null, selectedChoiceIds: null,
            orderedChoiceIds: [
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_35_ID), // Arsenal (wrong order)
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_34_ID), // Man City
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_36_ID), // Liverpool
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_37_ID), // Villa
            ]);
        $answer2->evaluate(333);
        $manager->persist($answer2);

        // USER_3: 2/3 correct = 667 points (Q19 correct, Q21 correct)
        $answer3 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_3_EXPIRED_CHALLENGE_6_ANSWER_ID),
            challenge: $challenge,
            user: $user3,
        );
        $answer3->answerQuestion($answeredAt, $question19, textAnswer: null, numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: [
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_30_ID), // Luton
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_31_ID), // Burnley
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_32_ID), // Sheffield Utd
            ], orderedChoiceIds: null);
        $answer3->answerQuestion($answeredAt, $question20, textAnswer: 'Arsenal', numericAnswer: null, // wrong
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer3->answerQuestion($answeredAt, $question21, textAnswer: null, numericAnswer: null,
            selectedChoiceId: null, selectedChoiceIds: null,
            orderedChoiceIds: [
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_34_ID), // Man City
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_35_ID), // Arsenal
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_36_ID), // Liverpool
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_37_ID), // Villa
            ]);
        $answer3->evaluate(667);
        $manager->persist($answer3);

        // USER_4: 1/3 correct = 333 points (only Q20 correct)
        $answer4 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_4_EXPIRED_CHALLENGE_6_ANSWER_ID),
            challenge: $challenge,
            user: $user4,
        );
        $answer4->answerQuestion($answeredAt, $question19, textAnswer: null, numericAnswer: null,
            selectedChoiceId: null,
            selectedChoiceIds: [
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_30_ID), // Luton (correct)
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_32_ID), // Sheffield Utd (correct)
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_33_ID), // Nottingham Forest (wrong)
            ], orderedChoiceIds: null);
        $answer4->answerQuestion($answeredAt, $question20, textAnswer: 'Manchester City', numericAnswer: null,
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer4->answerQuestion($answeredAt, $question21, textAnswer: null, numericAnswer: null,
            selectedChoiceId: null, selectedChoiceIds: null,
            orderedChoiceIds: [
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_36_ID), // Liverpool (wrong)
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_35_ID), // Arsenal
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_34_ID), // Man City
                Uuid::fromString(ExpiredChallenge6Fixture::CHOICE_37_ID), // Villa
            ]);
        $answer4->evaluate(333);
        $manager->persist($answer4);
    }

    /**
     * ExpiredChallenge7 - Golden Boot Quick Quiz (2 questions)
     * Q22: SingleSelect (Golden Boot winner) - Correct: Haaland (CHOICE_38)
     * Q23: Numeric (Haaland goals) - Correct: 27.0
     */
    private function createAnswersForChallenge7(
        ObjectManager $manager,
        User $user1,
        User $user2,
        User $user3,
        User $user4,
        Challenge $challenge,
        \DateTimeImmutable $answeredAt,
        Question $question22,
        Question $question23,
    ): void {
        // USER_1: 2/2 correct = 1000 points
        $answer1 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_1_EXPIRED_CHALLENGE_7_ANSWER_ID),
            challenge: $challenge,
            user: $user1,
        );
        $answer1->answerQuestion($answeredAt, $question22, textAnswer: null, numericAnswer: null,
            selectedChoiceId: Uuid::fromString(ExpiredChallenge7Fixture::CHOICE_38_ID), // Haaland (correct)
            selectedChoiceIds: null, orderedChoiceIds: null);
        $answer1->answerQuestion($answeredAt, $question23, textAnswer: null, numericAnswer: 27.0, // correct
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer1->evaluate(1000);
        $manager->persist($answer1);

        // USER_2: 1/2 correct = 500 points (Q22 wrong, Q23 correct)
        $answer2 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_2_EXPIRED_CHALLENGE_7_ANSWER_ID),
            challenge: $challenge,
            user: $user2,
        );
        $answer2->answerQuestion($answeredAt, $question22, textAnswer: null, numericAnswer: null,
            selectedChoiceId: Uuid::fromString(ExpiredChallenge7Fixture::CHOICE_39_ID), // Salah (wrong)
            selectedChoiceIds: null, orderedChoiceIds: null);
        $answer2->answerQuestion($answeredAt, $question23, textAnswer: null, numericAnswer: 27.0, // correct
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer2->evaluate(500);
        $manager->persist($answer2);

        // USER_3: 0/2 correct = 0 points (all wrong)
        $answer3 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_3_EXPIRED_CHALLENGE_7_ANSWER_ID),
            challenge: $challenge,
            user: $user3,
        );
        $answer3->answerQuestion($answeredAt, $question22, textAnswer: null, numericAnswer: null,
            selectedChoiceId: Uuid::fromString(ExpiredChallenge7Fixture::CHOICE_40_ID), // Watkins (wrong)
            selectedChoiceIds: null, orderedChoiceIds: null);
        $answer3->answerQuestion($answeredAt, $question23, textAnswer: null, numericAnswer: 15.0, // wrong
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer3->evaluate(0);
        $manager->persist($answer3);

        // USER_4: 2/2 correct = 1000 points
        $answer4 = new PlayerChallengeAnswer(
            id: Uuid::fromString(self::USER_4_EXPIRED_CHALLENGE_7_ANSWER_ID),
            challenge: $challenge,
            user: $user4,
        );
        $answer4->answerQuestion($answeredAt, $question22, textAnswer: null, numericAnswer: null,
            selectedChoiceId: Uuid::fromString(ExpiredChallenge7Fixture::CHOICE_38_ID), // Haaland (correct)
            selectedChoiceIds: null, orderedChoiceIds: null);
        $answer4->answerQuestion($answeredAt, $question23, textAnswer: null, numericAnswer: 27.0, // correct
            selectedChoiceId: null, selectedChoiceIds: null, orderedChoiceIds: null);
        $answer4->evaluate(1000);
        $manager->persist($answer4);
    }
}
