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
            CurrentChallenge1Fixture::class,
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

        // Load challenges
        $expiredChallenge1 = $manager->find(Challenge::class, Uuid::fromString(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID));
        assert($expiredChallenge1 !== null);
        $expiredChallenge2 = $manager->find(Challenge::class, Uuid::fromString(ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID));
        assert($expiredChallenge2 !== null);
        $expiredChallenge3 = $manager->find(Challenge::class, Uuid::fromString(ExpiredChallenge3Fixture::EXPIRED_CHALLENGE_3_ID));
        assert($expiredChallenge3 !== null);
        $currentChallenge1 = $manager->find(Challenge::class, Uuid::fromString(CurrentChallenge1Fixture::CURRENT_CHALLENGE_1_ID));
        assert($currentChallenge1 !== null);

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

        // User 1 (admin@example.com) - answers all three expired challenges
        $this->createAnswersForUser1($manager, $user1, $expiredChallenge1, $expiredChallenge2, $expiredChallenge3, $answeredAt, $question7, $question8, $question9, $question10, $question11);

        // User 2 (user@example.com) - answers both expired challenges
        $this->createAnswersForUser2($manager, $user2, $expiredChallenge1, $expiredChallenge2, $answeredAt, $question7, $question8, $question9, $question10);

        // User 3 (user3@example.com) - answers both expired challenges + 1 current challenge
        $this->createAnswersForUser3($manager, $user3, $expiredChallenge1, $expiredChallenge2, $currentChallenge1, $answeredAt, $question7, $question8, $question9, $question10, $question1, $question2, $question3);

        // User 4 has no answers - nothing to do

        // Evaluate only the first two expired challenges
        // Challenge 3 will remain unevaluated to test import evaluation
        $expiredChallenge1->evaluate($this->clock->now());
        $expiredChallenge2->evaluate($this->clock->now());

        $manager->flush();
    }

    private function createAnswersForUser1(
        ObjectManager $manager,
        User $user,
        Challenge $expiredChallenge1,
        Challenge $expiredChallenge2,
        Challenge $expiredChallenge3,
        \DateTimeImmutable $answeredAt,
        Question $question7,
        Question $question8,
        Question $question9,
        Question $question10,
        Question $question11,
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

        $answer2->evaluate(600);

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
    }

    private function createAnswersForUser2(
        ObjectManager $manager,
        User $user,
        Challenge $expiredChallenge1,
        Challenge $expiredChallenge2,
        \DateTimeImmutable $answeredAt,
        Question $question7,
        Question $question8,
        Question $question9,
        Question $question10,
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

        $answer2->evaluate(700);

        $manager->persist($answer2);
    }

    private function createAnswersForUser3(
        ObjectManager $manager,
        User $user,
        Challenge $expiredChallenge1,
        Challenge $expiredChallenge2,
        Challenge $currentChallenge1,
        \DateTimeImmutable $answeredAt,
        Question $question7,
        Question $question8,
        Question $question9,
        Question $question10,
        Question $question1,
        Question $question2,
        Question $question3,
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

        $answer2->evaluate(800);

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
    }
}
