<?php
declare(strict_types=1);

namespace FantasyAcademy\API\Tests\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use FantasyAcademy\API\Entity\Challenge;
use FantasyAcademy\API\Entity\Question;
use FantasyAcademy\API\Value\Answer;
use FantasyAcademy\API\Value\Choice;
use FantasyAcademy\API\Value\ChoiceQuestionConstraint;
use FantasyAcademy\API\Value\QuestionType;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

final class CurrentChallenge2Fixture extends Fixture
{
    public function __construct(
        readonly private ClockInterface $clock,
    ) {
    }

    public const string CURRENT_CHALLENGE_2_ID = '00000000-0000-0000-0002-000000000002';

    public const string QUESTION_4_ID = '00000000-0000-0000-0003-000000000004';
    public const string CHOICE_20_ID = '00000000-0000-0000-0004-000000000020';
    public const string CHOICE_21_ID = '00000000-0000-0000-0004-000000000021';

    public function load(ObjectManager $manager): void
    {
        $currentChallenge = new Challenge(
            id: Uuid::fromString(self::CURRENT_CHALLENGE_2_ID),
            name: 'Another exciting challenge',
            shortDescription: 'Very short description about the challenge',
            description: 'Some much longer description about the challenge',
            image: 'https://placecats.com/800/600',
            addedAt: $this->clock->now(),
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
            showStatisticsContinuously: true,
            gameweek: 4,
        );

        $manager->persist($currentChallenge);

        $question = new Question(
            id: Uuid::fromString(self::QUESTION_4_ID),
            challenge: $currentChallenge,
            text: 'What is your favorite color?',
            type: QuestionType::SingleSelect,
            image: 'https://placecats.com/600/400',
            numericConstraint: null,
            choiceConstraint: new ChoiceQuestionConstraint(
                choices: [
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_20_ID),
                        text: 'Red',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_21_ID),
                        text: 'Blue',
                    ),
                ],
            ),
            correctAnswer: new Answer(
                selectedChoiceId: Uuid::fromString(self::CHOICE_20_ID),
            ),
        );

        $manager->persist($question);

        $manager->flush();
    }
}
