<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Repository;

use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\Challenge;
use FantasyAcademy\API\Exceptions\ChallengeNotFound;
use Symfony\Component\Uid\Uuid;

readonly final class ChallengeRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function add(Challenge $challenge): void
    {
        $this->entityManager->persist($challenge);
    }

    /**
     * @throws ChallengeNotFound
     */
    public function get(Uuid $id): Challenge
    {
        $row = $this->entityManager->find(Challenge::class, $id);

        if ($row instanceof Challenge) {
            return $row;
        }

        throw new ChallengeNotFound();
    }
}
