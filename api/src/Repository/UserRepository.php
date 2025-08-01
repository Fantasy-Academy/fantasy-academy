<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Repository;

use Doctrine\ORM\NoResultException;
use FantasyAcademy\API\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Exceptions\UserNotFound;
use Symfony\Component\Uid\Uuid;

readonly final class UserRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function add(User $user): void
    {
        $this->entityManager->persist($user);
    }

    /**
     * @throws UserNotFound
     */
    public function get(string $email): User
    {
        try {
            $row = $this->entityManager->createQueryBuilder()
                ->from(User::class, 'u')
                ->select('u')
                ->where('u.email = :email')
                ->setParameter('email', $email)
                ->getQuery()
                ->getSingleResult();

            assert($row instanceof User);
            return $row;
        } catch (NoResultException) {
            throw new UserNotFound();
        }
    }

    /**
     * @throws UserNotFound
     */
    public function getById(Uuid $id): User
    {
        $user = $this->entityManager->find(User::class, $id);

        if ($user instanceof User) {
            return $user;
        }

        throw new UserNotFound();
    }
}
