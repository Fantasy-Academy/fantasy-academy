<?php
declare(strict_types=1);

namespace FantasyAcademy\API\Tests\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use FantasyAcademy\API\Entity\Challenge;
use FantasyAcademy\API\Entity\Question;
use FantasyAcademy\API\Value\Answer;
use FantasyAcademy\API\Value\QuestionType;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

final class ExpiredChallenge3Fixture extends Fixture
{
    public function __construct(
        readonly private ClockInterface $clock,
    ) {
    }

    public const string EXPIRED_CHALLENGE_3_ID = '00000000-0000-0000-0002-000000000006';

    public const string QUESTION_11_ID = '00000000-0000-0000-0003-000000000011';

    public function load(ObjectManager $manager): void
    {
        $expiredChallenge = new Challenge(
            id: Uuid::fromString(self::EXPIRED_CHALLENGE_3_ID),
            name: 'Third expired challenge (not evaluated)',
            shortDescription: 'This challenge is expired but not yet evaluated',
            description: 'This challenge will be used to test that the import process evaluates unevaluated challenges',
            image: 'https://placecats.com/800/600',
            addedAt: $this->clock->now(),
            startsAt: $this->clock->now()->modify('-4 weeks'),
            expiresAt: $this->clock->now()->modify('-1 day'),
            maxPoints: 1000,
            hintText: 'Need evaluation',
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

        $manager->persist($expiredChallenge);

        $question = new Question(
            id: Uuid::fromString(self::QUESTION_11_ID),
            challenge: $expiredChallenge,
            text: 'Unevaluated challenge question',
            type: QuestionType::Text,
            image: 'https://placecats.com/600/400',
            numericConstraint: null,
            choiceConstraint: null,
        );

        $question->correctAnswer = new Answer(
            textAnswer: 'Correct answer for unevaluated challenge',
        );

        $manager->persist($question);

        $manager->flush();
    }
}
