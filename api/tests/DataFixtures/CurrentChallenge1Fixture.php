<?php
declare(strict_types=1);

namespace FantasyAcademy\API\Tests\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use FantasyAcademy\API\Entity\Challenge;
use FantasyAcademy\API\Entity\Question;
use FantasyAcademy\API\Value\Choice;
use FantasyAcademy\API\Value\ChoiceQuestionConstraint;
use FantasyAcademy\API\Value\QuestionType;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

final class CurrentChallenge1Fixture extends Fixture
{
    public function __construct(
        readonly private ClockInterface $clock,
    ) {
    }

    public const string CURRENT_CHALLENGE_1_ID = '00000000-0000-0000-0002-000000000001';

    public const string QUESTION_1_ID = '00000000-0000-0000-0003-000000000001';
    public const string QUESTION_2_ID = '00000000-0000-0000-0003-000000000002';
    public const string QUESTION_3_ID = '00000000-0000-0000-0003-000000000003';

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
        $currentChallenge = new Challenge(
            id: Uuid::fromString(self::CURRENT_CHALLENGE_1_ID),
            name: 'Some exciting challenge',
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
            skillDiscipline: 20,
        );

        $manager->persist($currentChallenge);

        $question1 = new Question(
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

        $manager->persist($question1);

        $question2 = new Question(
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

        $manager->persist($question2);

        $question3 = new Question(
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

        $manager->persist($question3);

        $manager->flush();
    }
}
