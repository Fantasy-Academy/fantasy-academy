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

final class ExpiredChallenge4Fixture extends Fixture
{
    public function __construct(
        readonly private ClockInterface $clock,
    ) {
    }

    public const string EXPIRED_CHALLENGE_4_ID = '00000000-0000-0000-0002-000000000007';

    public const string QUESTION_12_ID = '00000000-0000-0000-0003-000000000012';
    public const string QUESTION_13_ID = '00000000-0000-0000-0003-000000000013';
    public const string QUESTION_14_ID = '00000000-0000-0000-0003-000000000014';
    public const string QUESTION_15_ID = '00000000-0000-0000-0003-000000000015';

    // Q12 - SingleSelect choices
    public const string CHOICE_14_ID = '00000000-0000-0000-0004-000000000014'; // Arsenal
    public const string CHOICE_15_ID = '00000000-0000-0000-0004-000000000015'; // Man City (correct)
    public const string CHOICE_16_ID = '00000000-0000-0000-0004-000000000016'; // Liverpool

    // Q13 - MultiSelect choices
    public const string CHOICE_17_ID = '00000000-0000-0000-0004-000000000017'; // Arsenal (correct)
    public const string CHOICE_18_ID = '00000000-0000-0000-0004-000000000018'; // Man City (correct)
    public const string CHOICE_19_ID = '00000000-0000-0000-0004-000000000019'; // Liverpool (correct)
    public const string CHOICE_22_ID = '00000000-0000-0000-0004-000000000022'; // Chelsea

    public function load(ObjectManager $manager): void
    {
        $expiredChallenge4 = new Challenge(
            id: Uuid::fromString(self::EXPIRED_CHALLENGE_4_ID),
            name: 'Premier League Predictions',
            shortDescription: 'Predict the outcome of this season',
            description: 'Make your predictions about the Premier League season. Who will win the title? Which teams will finish in the top 3? How many goals will be scored?',
            image: 'https://placecats.com/800/600',
            addedAt: $this->clock->now(),
            startsAt: $this->clock->now()->modify('-3 weeks'),
            expiresAt: $this->clock->now()->modify('-4 days'),
            maxPoints: 1000,
            hintText: 'Think about current form and squad depth',
            hintImage: null,
            skillAnalytical: 15,
            skillStrategicPlanning: 15,
            skillAdaptability: 10,
            skillPremierLeagueKnowledge: 20,
            skillRiskManagement: 10,
            skillDecisionMakingUnderPressure: 10,
            skillFinancialManagement: 5,
            skillLongTermVision: 15,
            gameweek: 2,
        );

        $manager->persist($expiredChallenge4);

        // Q12: SingleSelect - Who will win the Premier League?
        $question12 = new Question(
            id: Uuid::fromString(self::QUESTION_12_ID),
            challenge: $expiredChallenge4,
            text: 'Who will win the Premier League this season?',
            type: QuestionType::SingleSelect,
            image: null,
            numericConstraint: null,
            choiceConstraint: new ChoiceQuestionConstraint(
                choices: [
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_14_ID),
                        text: 'Arsenal',
                        description: 'The Gunners',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_15_ID),
                        text: 'Manchester City',
                        description: 'The Citizens',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_16_ID),
                        text: 'Liverpool',
                        description: 'The Reds',
                    ),
                ],
            ),
            correctAnswer: new Answer(
                selectedChoiceId: Uuid::fromString(self::CHOICE_15_ID), // Man City
            ),
        );

        $manager->persist($question12);

        // Q13: MultiSelect - Select the top 3 teams
        $question13 = new Question(
            id: Uuid::fromString(self::QUESTION_13_ID),
            challenge: $expiredChallenge4,
            text: 'Which teams will finish in the top 3?',
            type: QuestionType::MultiSelect,
            image: null,
            numericConstraint: null,
            choiceConstraint: new ChoiceQuestionConstraint(
                choices: [
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_17_ID),
                        text: 'Arsenal',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_18_ID),
                        text: 'Manchester City',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_19_ID),
                        text: 'Liverpool',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_22_ID),
                        text: 'Chelsea',
                    ),
                ],
                minSelections: 3,
                maxSelections: 3,
            ),
            correctAnswer: new Answer(
                selectedChoiceIds: [
                    Uuid::fromString(self::CHOICE_17_ID), // Arsenal
                    Uuid::fromString(self::CHOICE_18_ID), // Man City
                    Uuid::fromString(self::CHOICE_19_ID), // Liverpool
                ],
            ),
        );

        $manager->persist($question13);

        // Q14: Numeric - Total goals scored in the season
        $question14 = new Question(
            id: Uuid::fromString(self::QUESTION_14_ID),
            challenge: $expiredChallenge4,
            text: 'How many goals will be scored by the top scorer?',
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

        $manager->persist($question14);

        // Q15: Text - Name the top scorer
        $question15 = new Question(
            id: Uuid::fromString(self::QUESTION_15_ID),
            challenge: $expiredChallenge4,
            text: 'Name the Premier League top scorer',
            type: QuestionType::Text,
            image: null,
            numericConstraint: null,
            choiceConstraint: null,
            correctAnswer: new Answer(
                textAnswer: 'Haaland',
            ),
        );

        $manager->persist($question15);

        $manager->flush();
    }
}
