<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Query;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Ramsey\Uuid\UuidInterface;
use FantasyAcademy\API\Entity\StockItem;
use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Exceptions\StockItemNotFound;
use FantasyAcademy\API\Exceptions\UserNotFound;

readonly final class UserQuery
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws UserNotFound
     */
    public function getByApiToken(string $apiToken): User
    {
        try {
            /** @var User $row */
            $row = $this->entityManager->createQueryBuilder()
                ->from(User::class, 'u')
                ->select('u')
                ->where('u.apiToken = :apiToken')
                ->setParameter('apiToken', $apiToken)
                ->getQuery()
                ->getSingleResult();

            return $row;
        } catch (NoResultException $e) {
            throw new UserNotFound(previous: $e);
        }
    }

    /**
     * @return array<User>
     */
    public function getAll(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->from(User::class, 'u')
            ->select('u')
            ->getQuery()
            ->getResult();
    }
}
