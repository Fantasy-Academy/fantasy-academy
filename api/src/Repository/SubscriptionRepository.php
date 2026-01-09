<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Repository;

use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\Subscription;
use Symfony\Component\Uid\Uuid;

readonly final class SubscriptionRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function add(Subscription $subscription): void
    {
        $this->entityManager->persist($subscription);
    }

    public function findActiveByUser(Uuid $userId): ?Subscription
    {
        $result = $this->entityManager->createQueryBuilder()
            ->from(Subscription::class, 's')
            ->select('s')
            ->where('s.user = :userId')
            ->andWhere('s.status IN (:activeStatuses)')
            ->setParameter('userId', $userId)
            ->setParameter('activeStatuses', [
                Subscription::STATUS_ACTIVE,
                Subscription::STATUS_TRIALING,
            ])
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result instanceof Subscription ? $result : null;
    }

    public function findByStripeSubscriptionId(string $stripeSubscriptionId): ?Subscription
    {
        $result = $this->entityManager->createQueryBuilder()
            ->from(Subscription::class, 's')
            ->select('s')
            ->where('s.stripeSubscriptionId = :stripeSubscriptionId')
            ->setParameter('stripeSubscriptionId', $stripeSubscriptionId)
            ->getQuery()
            ->getOneOrNullResult();

        return $result instanceof Subscription ? $result : null;
    }

    public function findByStripeCustomerId(string $stripeCustomerId): ?Subscription
    {
        $result = $this->entityManager->createQueryBuilder()
            ->from(Subscription::class, 's')
            ->select('s')
            ->where('s.stripeCustomerId = :stripeCustomerId')
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults(1)
            ->setParameter('stripeCustomerId', $stripeCustomerId)
            ->getQuery()
            ->getOneOrNullResult();

        return $result instanceof Subscription ? $result : null;
    }
}
