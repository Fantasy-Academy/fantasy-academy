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

final class ExpiredChallenge5Fixture extends Fixture
{
    public function __construct(
        readonly private ClockInterface $clock,
    ) {
    }

    public const string EXPIRED_CHALLENGE_5_ID = '00000000-0000-0000-0002-000000000008';

    public const string QUESTION_16_ID = '00000000-0000-0000-0003-000000000016';
    public const string QUESTION_17_ID = '00000000-0000-0000-0003-000000000017';
    public const string QUESTION_18_ID = '00000000-0000-0000-0003-000000000018';

    // Q16 - Sort choices (players ranked by goals)
    public const string CHOICE_23_ID = '00000000-0000-0000-0004-000000000023'; // Haaland (1st)
    public const string CHOICE_24_ID = '00000000-0000-0000-0004-000000000024'; // Salah (2nd)
    public const string CHOICE_25_ID = '00000000-0000-0000-0004-000000000025'; // Son (3rd)
    public const string CHOICE_26_ID = '00000000-0000-0000-0004-000000000026'; // Saka (4th)

    // Q17 - SingleSelect choices (Best goalkeeper)
    public const string CHOICE_27_ID = '00000000-0000-0000-0004-000000000027'; // Alisson (correct)
    public const string CHOICE_28_ID = '00000000-0000-0000-0004-000000000028'; // Ederson
    public const string CHOICE_29_ID = '00000000-0000-0000-0004-000000000029'; // Raya

    public function load(ObjectManager $manager): void
    {
        $expiredChallenge5 = new Challenge(
            id: Uuid::fromString(self::EXPIRED_CHALLENGE_5_ID),
            name: 'Player Rankings Challenge',
            shortDescription: 'Rank the best Premier League players',
            description: 'Test your knowledge of Premier League player performances. Rank top scorers, predict clean sheets, and identify the best goalkeeper.',
            image: 'https://placecats.com/800/600',
            addedAt: $this->clock->now(),
            startsAt: $this->clock->now()->modify('-5 weeks'),
            expiresAt: $this->clock->now()->modify('-3 weeks'),
            maxPoints: 1000,
            hintText: 'Consider the current season statistics',
            hintImage: null,
            skillAnalytical: 20,
            skillStrategicPlanning: 10,
            skillAdaptability: 10,
            skillPremierLeagueKnowledge: 25,
            skillRiskManagement: 5,
            skillDecisionMakingUnderPressure: 10,
            skillFinancialManagement: 5,
            skillLongTermVision: 15,
            gameweek: 1,
        );

        $manager->persist($expiredChallenge5);

        // Q16: Sort - Rank players by goals scored
        $question16 = new Question(
            id: Uuid::fromString(self::QUESTION_16_ID),
            challenge: $expiredChallenge5,
            text: 'Rank these players by goals scored this season (highest first)',
            type: QuestionType::Sort,
            image: null,
            numericConstraint: null,
            choiceConstraint: new ChoiceQuestionConstraint(
                choices: [
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_23_ID),
                        text: 'Erling Haaland',
                        description: 'Manchester City striker',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_24_ID),
                        text: 'Mohamed Salah',
                        description: 'Liverpool forward',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_25_ID),
                        text: 'Heung-min Son',
                        description: 'Tottenham forward',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_26_ID),
                        text: 'Bukayo Saka',
                        description: 'Arsenal winger',
                    ),
                ],
                minSelections: 4,
                maxSelections: 4,
            ),
            correctAnswer: new Answer(
                orderedChoiceIds: [
                    Uuid::fromString(self::CHOICE_23_ID), // Haaland
                    Uuid::fromString(self::CHOICE_24_ID), // Salah
                    Uuid::fromString(self::CHOICE_25_ID), // Son
                    Uuid::fromString(self::CHOICE_26_ID), // Saka
                ],
            ),
        );

        $manager->persist($question16);

        // Q17: SingleSelect - Best goalkeeper
        $question17 = new Question(
            id: Uuid::fromString(self::QUESTION_17_ID),
            challenge: $expiredChallenge5,
            text: 'Who is the best goalkeeper in the Premier League?',
            type: QuestionType::SingleSelect,
            image: null,
            numericConstraint: null,
            choiceConstraint: new ChoiceQuestionConstraint(
                choices: [
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_27_ID),
                        text: 'Alisson',
                        description: 'Liverpool goalkeeper',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_28_ID),
                        text: 'Ederson',
                        description: 'Manchester City goalkeeper',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_29_ID),
                        text: 'David Raya',
                        description: 'Arsenal goalkeeper',
                    ),
                ],
            ),
            correctAnswer: new Answer(
                selectedChoiceId: Uuid::fromString(self::CHOICE_27_ID), // Alisson
            ),
        );

        $manager->persist($question17);

        // Q18: Numeric - Clean sheets prediction
        $question18 = new Question(
            id: Uuid::fromString(self::QUESTION_18_ID),
            challenge: $expiredChallenge5,
            text: 'How many clean sheets will the Golden Glove winner have?',
            type: QuestionType::Numeric,
            image: null,
            numericConstraint: new NumericQuestionConstraint(
                min: 0,
                max: 30,
            ),
            choiceConstraint: null,
            correctAnswer: new Answer(
                numericAnswer: 15.0,
            ),
        );

        $manager->persist($question18);

        $manager->flush();
    }
}
