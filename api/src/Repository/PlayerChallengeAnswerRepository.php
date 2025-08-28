<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Repository;

use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\PlayerChallengeAnswer;
use FantasyAcademy\API\Entity\Question;
use FantasyAcademy\API\Exceptions\PlayerChallengeAnswerNotFound;
use FantasyAcademy\API\Exceptions\QuestionNotFound;
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

    /**
     * @throws PlayerChallengeAnswerNotFound
     */
    public function get(Uuid $id): PlayerChallengeAnswer
    {
        $row = $this->entityManager->find(PlayerChallengeAnswer::class, $id);

        if ($row instanceof PlayerChallengeAnswer) {
            return $row;
        }

        throw new PlayerChallengeAnswerNotFound();
    }

    public function find(Uuid $userId, Uuid $challengeId): null|PlayerChallengeAnswer
    {
        /** @var null|PlayerChallengeAnswer $row */
        $row = $this->entityManager->createQueryBuilder()
            ->select('player_challenge_answer')
            ->from(PlayerChallengeAnswer::class, 'player_challenge_answer')
            ->where('player_challenge_answer.user = :userId')
            ->andWhere('player_challenge_answer.challenge = :challengeId')
            ->setParameter('userId', $userId)
            ->setParameter('challengeId', $challengeId)
            ->getQuery()
            ->getOneOrNullResult();

        return $row;
    }
}
