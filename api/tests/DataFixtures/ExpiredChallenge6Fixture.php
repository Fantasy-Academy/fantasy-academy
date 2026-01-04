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

final class ExpiredChallenge6Fixture extends Fixture
{
    public function __construct(
        readonly private ClockInterface $clock,
    ) {
    }

    public const string EXPIRED_CHALLENGE_6_ID = '00000000-0000-0000-0002-000000000009';

    public const string QUESTION_19_ID = '00000000-0000-0000-0003-000000000019';
    public const string QUESTION_20_ID = '00000000-0000-0000-0003-000000000020';
    public const string QUESTION_21_ID = '00000000-0000-0000-0003-000000000021';

    // Q19 - MultiSelect choices (Relegated teams)
    public const string CHOICE_30_ID = '00000000-0000-0000-0004-000000000030'; // Luton (correct)
    public const string CHOICE_31_ID = '00000000-0000-0000-0004-000000000031'; // Burnley (correct)
    public const string CHOICE_32_ID = '00000000-0000-0000-0004-000000000032'; // Sheffield Utd (correct)
    public const string CHOICE_33_ID = '00000000-0000-0000-0004-000000000033'; // Nottingham Forest

    // Q21 - Sort choices (League position order)
    public const string CHOICE_34_ID = '00000000-0000-0000-0004-000000000034'; // Man City (1st)
    public const string CHOICE_35_ID = '00000000-0000-0000-0004-000000000035'; // Arsenal (2nd)
    public const string CHOICE_36_ID = '00000000-0000-0000-0004-000000000036'; // Liverpool (3rd)
    public const string CHOICE_37_ID = '00000000-0000-0000-0004-000000000037'; // Aston Villa (4th)

    public function load(ObjectManager $manager): void
    {
        $expiredChallenge6 = new Challenge(
            id: Uuid::fromString(self::EXPIRED_CHALLENGE_6_ID),
            name: 'Season Finale Predictions',
            shortDescription: 'Predict relegation and final standings',
            description: 'The season is coming to an end. Predict which teams will be relegated and the final league standings.',
            image: 'https://placecats.com/800/600',
            addedAt: $this->clock->now(),
            startsAt: $this->clock->now()->modify('-2 weeks'),
            expiresAt: $this->clock->now()->modify('-8 days'),
            maxPoints: 1000,
            hintText: 'Look at the current standings and remaining fixtures',
            hintImage: null,
            skillAnalytical: 15,
            skillStrategicPlanning: 15,
            skillAdaptability: 15,
            skillPremierLeagueKnowledge: 20,
            skillRiskManagement: 10,
            skillDecisionMakingUnderPressure: 10,
            skillFinancialManagement: 5,
            skillLongTermVision: 10,
            gameweek: 3,
        );

        $manager->persist($expiredChallenge6);

        // Q19: MultiSelect - Select relegated teams
        $question19 = new Question(
            id: Uuid::fromString(self::QUESTION_19_ID),
            challenge: $expiredChallenge6,
            text: 'Which three teams will be relegated this season?',
            type: QuestionType::MultiSelect,
            image: null,
            numericConstraint: null,
            choiceConstraint: new ChoiceQuestionConstraint(
                choices: [
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_30_ID),
                        text: 'Luton Town',
                        description: 'The Hatters',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_31_ID),
                        text: 'Burnley',
                        description: 'The Clarets',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_32_ID),
                        text: 'Sheffield United',
                        description: 'The Blades',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_33_ID),
                        text: 'Nottingham Forest',
                        description: 'The Tricky Trees',
                    ),
                ],
                minSelections: 3,
                maxSelections: 3,
            ),
            correctAnswer: new Answer(
                selectedChoiceIds: [
                    Uuid::fromString(self::CHOICE_30_ID), // Luton
                    Uuid::fromString(self::CHOICE_31_ID), // Burnley
                    Uuid::fromString(self::CHOICE_32_ID), // Sheffield Utd
                ],
            ),
        );

        $manager->persist($question19);

        // Q20: Text - Name the PL champion
        $question20 = new Question(
            id: Uuid::fromString(self::QUESTION_20_ID),
            challenge: $expiredChallenge6,
            text: 'Name the Premier League champion',
            type: QuestionType::Text,
            image: null,
            numericConstraint: null,
            choiceConstraint: null,
            correctAnswer: new Answer(
                textAnswer: 'Manchester City',
            ),
        );

        $manager->persist($question20);

        // Q21: Sort - Order by league position
        $question21 = new Question(
            id: Uuid::fromString(self::QUESTION_21_ID),
            challenge: $expiredChallenge6,
            text: 'Order these teams by their final league position (1st to 4th)',
            type: QuestionType::Sort,
            image: null,
            numericConstraint: null,
            choiceConstraint: new ChoiceQuestionConstraint(
                choices: [
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_34_ID),
                        text: 'Manchester City',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_35_ID),
                        text: 'Arsenal',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_36_ID),
                        text: 'Liverpool',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_37_ID),
                        text: 'Aston Villa',
                    ),
                ],
                minSelections: 4,
                maxSelections: 4,
            ),
            correctAnswer: new Answer(
                orderedChoiceIds: [
                    Uuid::fromString(self::CHOICE_34_ID), // Man City (1st)
                    Uuid::fromString(self::CHOICE_35_ID), // Arsenal (2nd)
                    Uuid::fromString(self::CHOICE_36_ID), // Liverpool (3rd)
                    Uuid::fromString(self::CHOICE_37_ID), // Villa (4th)
                ],
            ),
        );

        $manager->persist($question21);

        $manager->flush();
    }
}
