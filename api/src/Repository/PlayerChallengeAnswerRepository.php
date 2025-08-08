<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Repository;

use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\PlayerChallengeAnswer;
use Symfony\Component\Uid\Uuid;

readonly final class PlayerChallengeAnswerRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(PlayerChallengeAnswer $question): void
    {
        $this->entityManager->persist($question);
    }

    public function find(Uuid $userId, Uuid $challengeId): null|PlayerChallengeAnswer
    {
        return $this->entityManager->createQueryBuilder()
            ->select('player_challenge_answer')
            ->from(PlayerChallengeAnswer::class, 'player_challenge_answer')
            ->where('player_challenge_answer.user = :userId')
            ->andWhere('player_challenge_answer.challenge = :challengeId')
            ->setParameter('userId', $userId)
            ->setParameter('challengeId', $challengeId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
