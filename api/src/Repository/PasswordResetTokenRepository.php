<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Repository;

use FantasyAcademy\API\Entity\PasswordResetToken;
use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Exceptions\PasswordResetTokenNotFound;

readonly final class PasswordResetTokenRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function add(PasswordResetToken $token): void
    {
        $this->entityManager->persist($token);
    }

    /**
     * @throws PasswordResetTokenNotFound
     */
    public function get(string $tokenId): PasswordResetToken
    {
        $token = $this->entityManager->find(PasswordResetToken::class, $tokenId);

        if ($token instanceof PasswordResetToken) {
            return $token;
        }

        throw new PasswordResetTokenNotFound();
    }
}
