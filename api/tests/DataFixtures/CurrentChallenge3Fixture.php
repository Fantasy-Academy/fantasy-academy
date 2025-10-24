<?php
declare(strict_types=1);

namespace FantasyAcademy\API\Tests\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use FantasyAcademy\API\Entity\Challenge;
use FantasyAcademy\API\Entity\Question;
use FantasyAcademy\API\Value\NumericQuestionConstraint;
use FantasyAcademy\API\Value\QuestionType;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

final class CurrentChallenge3Fixture extends Fixture
{
    public function __construct(
        readonly private ClockInterface $clock,
    ) {
    }

    public const string CURRENT_CHALLENGE_3_ID = '00000000-0000-0000-0002-000000000003';

    public const string QUESTION_5_ID = '00000000-0000-0000-0003-000000000005';
    public const string QUESTION_6_ID = '00000000-0000-0000-0003-000000000006';

    public function load(ObjectManager $manager): void
    {
        $currentChallenge = new Challenge(
            id: Uuid::fromString(self::CURRENT_CHALLENGE_3_ID),
            name: 'And one more exciting challenge',
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
        );

        $manager->persist($currentChallenge);

        $question5 = new Question(
            id: Uuid::fromString(self::QUESTION_5_ID),
            challenge: $currentChallenge,
            text: 'Write something',
            type: QuestionType::Text,
            image: 'https://placecats.com/600/400',
            numericConstraint: null,
            choiceConstraint: null,
        );

        $manager->persist($question5);

        $question6 = new Question(
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

        $manager->persist($question6);

        $manager->flush();
    }
}
