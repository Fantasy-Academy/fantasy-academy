<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Repository;

use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\Gameweek;
use FantasyAcademy\API\Exceptions\GameweekNotFound;
use Symfony\Component\Uid\Uuid;

readonly final class GameweekRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function add(Gameweek $gameweek): void
    {
        $this->entityManager->persist($gameweek);
    }

    /**
     * @throws GameweekNotFound
     */
    public function get(Uuid $id): Gameweek
    {
        $row = $this->entityManager->find(Gameweek::class, $id);

        if ($row instanceof Gameweek) {
            return $row;
        }

        throw new GameweekNotFound();
    }

    public function remove(Gameweek $gameweek): void
    {
        $this->entityManager->remove($gameweek);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
