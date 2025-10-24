<?php
declare(strict_types=1);

namespace FantasyAcademy\API\Tests\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use FantasyAcademy\API\Entity\Challenge;
use FantasyAcademy\API\Entity\Question;
use FantasyAcademy\API\Value\QuestionType;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

final class ExpiredChallengeFixture extends Fixture
{
    public function __construct(
        readonly private ClockInterface $clock,
    ) {
    }

    public const string EXPIRED_CHALLENGE_ID = '00000000-0000-0000-0002-000000000004';

    public const string QUESTION_7_ID = '00000000-0000-0000-0003-000000000007';

    public function load(ObjectManager $manager): void
    {
        $expiredChallenge = new Challenge(
            id: Uuid::fromString(self::EXPIRED_CHALLENGE_ID),
            name: 'Some expired challenge',
            shortDescription: 'Very short description about the expired challenge',
            description: 'Some much longer description about the expired challenge',
            image: 'https://placecats.com/800/600',
            addedAt: $this->clock->now(),
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

        $manager->flush();
    }
}
