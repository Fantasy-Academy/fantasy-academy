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
use FantasyAcademy\API\Value\NumericQuestionConstraint;
use FantasyAcademy\API\Value\QuestionType;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

final class ExpiredChallenge7Fixture extends Fixture
{
    public function __construct(
        readonly private ClockInterface $clock,
    ) {
    }

    public const string EXPIRED_CHALLENGE_7_ID = '00000000-0000-0000-0002-000000000010';

    public const string QUESTION_22_ID = '00000000-0000-0000-0003-000000000022';
    public const string QUESTION_23_ID = '00000000-0000-0000-0003-000000000023';

    // Q22 - SingleSelect choices (Golden Boot winner)
    public const string CHOICE_38_ID = '00000000-0000-0000-0004-000000000038'; // Haaland (correct)
    public const string CHOICE_39_ID = '00000000-0000-0000-0004-000000000039'; // Salah
    public const string CHOICE_40_ID = '00000000-0000-0000-0004-000000000040'; // Watkins
    public const string CHOICE_41_ID = '00000000-0000-0000-0004-000000000041'; // Palmer

    public function load(ObjectManager $manager): void
    {
        $expiredChallenge7 = new Challenge(
            id: Uuid::fromString(self::EXPIRED_CHALLENGE_7_ID),
            name: 'Golden Boot Quick Quiz',
            shortDescription: 'Quick predictions on the Golden Boot race',
            description: 'A quick challenge about the Premier League Golden Boot race. Two questions, double the fun!',
            image: 'https://placecats.com/800/600',
            addedAt: $this->clock->now(),
            startsAt: $this->clock->now()->modify('-2 weeks'),
            expiresAt: $this->clock->now()->modify('-5 days'),
            maxPoints: 1000,
            hintText: 'Who has been the most consistent scorer?',
            hintImage: null,
            skillAnalytical: 10,
            skillStrategicPlanning: 10,
            skillAdaptability: 10,
            skillPremierLeagueKnowledge: 30,
            skillRiskManagement: 10,
            skillDecisionMakingUnderPressure: 10,
            skillFinancialManagement: 10,
            skillLongTermVision: 10,
            gameweek: 2,
        );

        $manager->persist($expiredChallenge7);

        // Q22: SingleSelect - Golden Boot winner
        $question22 = new Question(
            id: Uuid::fromString(self::QUESTION_22_ID),
            challenge: $expiredChallenge7,
            text: 'Who will win the Golden Boot this season?',
            type: QuestionType::SingleSelect,
            image: null,
            numericConstraint: null,
            choiceConstraint: new ChoiceQuestionConstraint(
                choices: [
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_38_ID),
                        text: 'Erling Haaland',
                        description: 'Manchester City',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_39_ID),
                        text: 'Mohamed Salah',
                        description: 'Liverpool',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_40_ID),
                        text: 'Ollie Watkins',
                        description: 'Aston Villa',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_41_ID),
                        text: 'Cole Palmer',
                        description: 'Chelsea',
                    ),
                ],
            ),
            correctAnswer: new Answer(
                selectedChoiceId: Uuid::fromString(self::CHOICE_38_ID), // Haaland
            ),
        );

        $manager->persist($question22);

        // Q23: Numeric - Total Haaland goals
        $question23 = new Question(
            id: Uuid::fromString(self::QUESTION_23_ID),
            challenge: $expiredChallenge7,
            text: 'How many Premier League goals will Haaland score this season?',
            type: QuestionType::Numeric,
            image: null,
            numericConstraint: new NumericQuestionConstraint(
                min: 0,
                max: 50,
            ),
            choiceConstraint: null,
            correctAnswer: new Answer(
                numericAnswer: 27.0,
            ),
        );

        $manager->persist($question23);

        $manager->flush();
    }
}
