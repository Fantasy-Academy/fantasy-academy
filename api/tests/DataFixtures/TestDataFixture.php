<?php
declare(strict_types=1);

namespace FantasyAcademy\API\Tests\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use FantasyAcademy\API\Entity\Challenge;
use FantasyAcademy\API\Entity\Question;
use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Value\Choice;
use FantasyAcademy\API\Value\ChoiceQuestionConstraint;
use FantasyAcademy\API\Value\NumericQuestionConstraint;
use FantasyAcademy\API\Value\QuestionType;
use Psr\Clock\ClockInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

final class TestDataFixture extends Fixture
{
    public function __construct(
        readonly private ClockInterface $clock,
        readonly private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public const string USER_PASSWORD = 'pass';

    public const string USER_1_ID = '00000000-0000-0000-0001-000000000001';
    public const string USER_1_EMAIL = 'admin@example.com';

    public const string USER_2_ID = '00000000-0000-0000-0001-000000000002';
    public const string USER_2_EMAIL = 'user@example.com';

    public const string CURRENT_CHALLENGE_1_ID = '00000000-0000-0000-0002-000000000001';
    public const string CURRENT_CHALLENGE_2_ID = '00000000-0000-0000-0002-000000000002';
    public const string CURRENT_CHALLENGE_3_ID = '00000000-0000-0000-0002-000000000003';
    public const string EXPIRED_CHALLENGE_ID = '00000000-0000-0000-0002-000000000004';

    public const string QUESTION_1_ID = '00000000-0000-0000-0003-000000000001';
    public const string QUESTION_2_ID = '00000000-0000-0000-0003-000000000002';
    public const string QUESTION_3_ID = '00000000-0000-0000-0003-000000000003';
    public const string QUESTION_4_ID = '00000000-0000-0000-0003-000000000004';
    public const string QUESTION_5_ID = '00000000-0000-0000-0003-000000000005';
    public const string QUESTION_6_ID = '00000000-0000-0000-0003-000000000006';
    public const string QUESTION_7_ID = '00000000-0000-0000-0003-000000000007';

    public const string CHOICE_1_ID = '00000000-0000-0000-0004-000000000001';
    public const string CHOICE_2_ID = '00000000-0000-0000-0004-000000000002';
    public const string CHOICE_3_ID = '00000000-0000-0000-0004-000000000003';
    public const string CHOICE_4_ID = '00000000-0000-0000-0004-000000000004';
    public const string CHOICE_5_ID = '00000000-0000-0000-0004-000000000005';
    public const string CHOICE_6_ID = '00000000-0000-0000-0004-000000000006';
    public const string CHOICE_7_ID = '00000000-0000-0000-0004-000000000007';
    public const string CHOICE_8_ID = '00000000-0000-0000-0004-000000000008';

    public function load(ObjectManager $manager): void
    {
        $this->createUsers($manager);
        $this->createChallenges($manager);

        $manager->flush();
    }

    private function createUsers(ObjectManager $manager): void
    {
        $registeredAt = $this->clock->now()->modify('-1 week');

        $user1 = new User(
            Uuid::fromString(self::USER_1_ID),
            self::USER_1_EMAIL,
            $registeredAt,
            'User 1',
            true,
            [User::ROLE_ADMIN],
        );

        $hashedPassword = $this->passwordHasher->hashPassword($user1, self::USER_PASSWORD);
        $user1->changePassword($hashedPassword);

        $manager->persist($user1);


        $user2 = new User(
            Uuid::fromString(self::USER_2_ID),
            self::USER_2_EMAIL,
            $registeredAt,
            'User 2',
            true,
            [User::ROLE_USER],
        );

        $hashedPassword = $this->passwordHasher->hashPassword($user2, self::USER_PASSWORD);
        $user2->changePassword($hashedPassword);

        $manager->persist($user2);
    }

    private function createChallenges(ObjectManager $manager): void
    {
        $currentChallenge = new Challenge(
            id: Uuid::fromString(self::CURRENT_CHALLENGE_1_ID),
            name: 'Some exciting challenge',
            shortDescription: 'Very short description about the challenge',
            description: 'Some much longer description about the challenge',
            image: 'https://placecats.com/800/600',
            startsAt: $this->clock->now()->modify('-1 week'),
            expiresAt: $this->clock->now()->modify('+1 week'),
            maxPoints: 1000,
            hintText: 'Something helpful',
            hintImage: 'https://placecats.com/800/600',
            skillAnalytical: 10,
            skillStrategicPlanning: 10,
            skillAdaptability: 10,
            skillPremierLeagueKnowledge: 10,
            skillRiskManagement: 10,
            skillDecisionMakingUnderPressure: 10,
            skillFinancialManagement: 10,
            skillLongTermVision: 10,
            skillDiscipline: 20,
        );

        $manager->persist($currentChallenge);

        $question = new Question(
            id: Uuid::fromString(self::QUESTION_1_ID),
            challenge: $currentChallenge,
            text: 'How are you?',
            type: QuestionType::SingleSelect,
            image: 'https://placecats.com/600/400',
            numericConstraint: null,
            choiceConstraint: new ChoiceQuestionConstraint(
                choices: [
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_1_ID),
                        text: 'Im good',
                        description: 'Description of good?',
                        image: 'https://placecats.com/400/300',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_2_ID),
                        text: 'Bad',
                        description: 'Description of bad?',
                        image: 'https://placecats.com/400/300',
                    ),
                ],
            ),
        );

        $manager->persist($question);

        $question = new Question(
            id: Uuid::fromString(self::QUESTION_2_ID),
            challenge: $currentChallenge,
            text: 'Pick some numbers',
            type: QuestionType::MultiSelect,
            image: 'https://placecats.com/600/400',
            numericConstraint: null,
            choiceConstraint: new ChoiceQuestionConstraint(
                choices: [
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_3_ID),
                        text: '42',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_4_ID),
                        text: '420',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_5_ID),
                        text: '666',
                    ),
                ],
                minSelections: 1,
                maxSelections: 3,
            ),
        );

        $manager->persist($question);

        $question = new Question(
            id: Uuid::fromString(self::QUESTION_3_ID),
            challenge: $currentChallenge,
            text: 'Sort those',
            type: QuestionType::Sort,
            image: 'https://placecats.com/600/400',
            numericConstraint: null,
            choiceConstraint: new ChoiceQuestionConstraint(
                choices: [
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_6_ID),
                        text: '42',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_7_ID),
                        text: '420',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_8_ID),
                        text: '666',
                    ),
                ],
                minSelections: 1,
                maxSelections: 3,
            ),
        );

        $manager->persist($question);

        $currentChallenge = new Challenge(
            id: Uuid::fromString(self::CURRENT_CHALLENGE_2_ID),
            name: 'Another exciting challenge',
            shortDescription: 'Very short description about the challenge',
            description: 'Some much longer description about the challenge',
            image: 'https://placecats.com/800/600',
            startsAt: $this->clock->now()->modify('-1 week'),
            expiresAt: $this->clock->now()->modify('+1 week'),
            maxPoints: 1000,
            hintText: 'Something helpful',
            hintImage: 'https://placecats.com/800/600',
            skillAnalytical: 10,
            skillStrategicPlanning: 10,
            skillAdaptability: 10,
            skillPremierLeagueKnowledge: 10,
            skillRiskManagement: 10,
            skillDecisionMakingUnderPressure: 10,
            skillFinancialManagement: 10,
            skillLongTermVision: 10,
            skillDiscipline: 20,
        );

        $manager->persist($currentChallenge);

        $question = new Question(
            id: Uuid::fromString(self::QUESTION_4_ID),
            challenge: $currentChallenge,
            text: 'Sort those',
            type: QuestionType::Sort,
            image: 'https://placecats.com/600/400',
            numericConstraint: null,
            choiceConstraint: new ChoiceQuestionConstraint(
                choices: [
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_6_ID),
                        text: '42',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_7_ID),
                        text: '420',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_8_ID),
                        text: '666',
                    ),
                ],
            ),
        );

        $manager->persist($question);

        $currentChallenge = new Challenge(
            id: Uuid::fromString(self::CURRENT_CHALLENGE_3_ID),
            name: 'And one more exciting challenge',
            shortDescription: 'Very short description about the challenge',
            description: 'Some much longer description about the challenge',
            image: 'https://placecats.com/800/600',
            startsAt: $this->clock->now()->modify('-1 week'),
            expiresAt: $this->clock->now()->modify('+1 week'),
            maxPoints: 1000,
            hintText: 'Something helpful',
            hintImage: 'https://placecats.com/800/600',
            skillAnalytical: 10,
            skillStrategicPlanning: 10,
            skillAdaptability: 10,
            skillPremierLeagueKnowledge: 10,
            skillRiskManagement: 10,
            skillDecisionMakingUnderPressure: 10,
            skillFinancialManagement: 10,
            skillLongTermVision: 10,
            skillDiscipline: 20,
        );

        $manager->persist($currentChallenge);

        $question = new Question(
            id: Uuid::fromString(self::QUESTION_5_ID),
            challenge: $currentChallenge,
            text: 'Write something',
            type: QuestionType::Text,
            image: 'https://placecats.com/600/400',
            numericConstraint: null,
            choiceConstraint: null,
        );

        $manager->persist($question);

        $question = new Question(
            id: Uuid::fromString(self::QUESTION_6_ID),
            challenge: $currentChallenge,
            text: 'Write something numeric',
            type: QuestionType::Numeric,
            image: 'https://placecats.com/600/400',
            numericConstraint: new NumericQuestionConstraint(
                min: 1,
                max: 42,
            ),
            choiceConstraint: null,
        );

        $manager->persist($question);

        $expiredChallenge = new Challenge(
            id: Uuid::fromString(self::EXPIRED_CHALLENGE_ID),
            name: 'Some expired challenge',
            shortDescription: 'Very short description about the expired challenge',
            description: 'Some much longer description about the expired challenge',
            image: 'https://placecats.com/800/600',
            startsAt: $this->clock->now()->modify('-2 weeks'),
            expiresAt: $this->clock->now()->modify('-1 day'),
            maxPoints: 1000,
            hintText: 'Something not that helpful',
            hintImage: 'https://placecats.com/800/600',
            skillAnalytical: 10,
            skillStrategicPlanning: 10,
            skillAdaptability: 10,
            skillPremierLeagueKnowledge: 10,
            skillRiskManagement: 10,
            skillDecisionMakingUnderPressure: 10,
            skillFinancialManagement: 10,
            skillLongTermVision: 10,
            skillDiscipline: 20,
        );

        $manager->persist($expiredChallenge);

        $question = new Question(
            id: Uuid::fromString(self::QUESTION_7_ID),
            challenge: $expiredChallenge,
            text: 'Some dummy expired question',
            type: QuestionType::Text,
            image: 'https://placecats.com/600/400',
            numericConstraint: null,
            choiceConstraint: null,
        );

        $manager->persist($question);
    }
}
