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

final class ExpiredChallenge2Fixture extends Fixture
{
    public function __construct(
        readonly private ClockInterface $clock,
    ) {
    }

    public const string EXPIRED_CHALLENGE_2_ID = '00000000-0000-0000-0002-000000000005';

    public const string QUESTION_8_ID = '00000000-0000-0000-0003-000000000008';
    public const string QUESTION_9_ID = '00000000-0000-0000-0003-000000000009';
    public const string QUESTION_10_ID = '00000000-0000-0000-0003-000000000010';

    public const string CHOICE_9_ID = '00000000-0000-0000-0004-000000000009';
    public const string CHOICE_10_ID = '00000000-0000-0000-0004-000000000010';
    public const string CHOICE_11_ID = '00000000-0000-0000-0004-000000000011';
    public const string CHOICE_12_ID = '00000000-0000-0000-0004-000000000012';
    public const string CHOICE_13_ID = '00000000-0000-0000-0004-000000000013';

    public function load(ObjectManager $manager): void
    {
        $expiredChallenge2 = new Challenge(
            id: Uuid::fromString(self::EXPIRED_CHALLENGE_2_ID),
            name: 'Another expired challenge',
            shortDescription: 'Very short description about another expired challenge',
            description: 'Some much longer description about another expired challenge',
            image: 'https://placecats.com/800/600',
            addedAt: $this->clock->now(),
            startsAt: $this->clock->now()->modify('-3 weeks'),
            expiresAt: $this->clock->now()->modify('-1 day'),
            maxPoints: 1000,
            hintText: 'This hint came too late',
            hintImage: 'https://placecats.com/800/600',
            skillAnalytical: 10,
            skillStrategicPlanning: 10,
            skillAdaptability: 10,
            skillPremierLeagueKnowledge: 10,
            skillRiskManagement: 10,
            skillDecisionMakingUnderPressure: 10,
            skillFinancialManagement: 10,
            skillLongTermVision: 10,
        );

        $manager->persist($expiredChallenge2);

        $question8 = new Question(
            id: Uuid::fromString(self::QUESTION_8_ID),
            challenge: $expiredChallenge2,
            text: 'What is your favorite color?',
            type: QuestionType::SingleSelect,
            image: 'https://placecats.com/600/400',
            numericConstraint: null,
            choiceConstraint: new ChoiceQuestionConstraint(
                choices: [
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_9_ID),
                        text: 'Red',
                        description: 'The color of passion',
                        image: 'https://placecats.com/400/300',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_10_ID),
                        text: 'Blue',
                        description: 'The color of calm',
                        image: 'https://placecats.com/400/300',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_11_ID),
                        text: 'Green',
                        description: 'The color of nature',
                        image: 'https://placecats.com/400/300',
                    ),
                ],
            ),
            correctAnswer: new Answer(
                selectedChoiceId: Uuid::fromString(self::CHOICE_9_ID), // Red
            ),
        );

        $manager->persist($question8);

        $question9 = new Question(
            id: Uuid::fromString(self::QUESTION_9_ID),
            challenge: $expiredChallenge2,
            text: 'Pick your favorite numbers',
            type: QuestionType::MultiSelect,
            image: 'https://placecats.com/600/400',
            numericConstraint: null,
            choiceConstraint: new ChoiceQuestionConstraint(
                choices: [
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_12_ID),
                        text: '7',
                    ),
                    new Choice(
                        id: Uuid::fromString(self::CHOICE_13_ID),
                        text: '13',
                    ),
                ],
                minSelections: 1,
                maxSelections: 2,
            ),
            correctAnswer: new Answer(
                selectedChoiceIds: [
                    Uuid::fromString(self::CHOICE_12_ID), // 7
                    Uuid::fromString(self::CHOICE_13_ID), // 13
                ],
            ),
        );

        $manager->persist($question9);

        $question10 = new Question(
            id: Uuid::fromString(self::QUESTION_10_ID),
            challenge: $expiredChallenge2,
            text: 'Enter a number between 1 and 100',
            type: QuestionType::Numeric,
            image: 'https://placecats.com/600/400',
            numericConstraint: new NumericQuestionConstraint(
                min: 1,
                max: 100,
            ),
            choiceConstraint: null,
            correctAnswer: new Answer(
                numericAnswer: 42.0,
            ),
        );

        $manager->persist($question10);

        $manager->flush();
    }
}
