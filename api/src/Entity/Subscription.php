<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use JetBrains\PhpStorm\Immutable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Index(columns: ['stripe_subscription_id'])]
#[Index(columns: ['stripe_customer_id'])]
class Subscription
{
    public const string STATUS_ACTIVE = 'active';
    public const string STATUS_CANCELED = 'canceled';
    public const string STATUS_PAST_DUE = 'past_due';
    public const string STATUS_TRIALING = 'trialing';
    public const string STATUS_INCOMPLETE = 'incomplete';
    public const string STATUS_INCOMPLETE_EXPIRED = 'incomplete_expired';
    public const string STATUS_UNPAID = 'unpaid';
    public const string STATUS_PAUSED = 'paused';

    #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
    #[Column(length: 50)]
    public string $status;

    #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
    #[Column(type: Types::DATETIME_IMMUTABLE)]
    public DateTimeImmutable $currentPeriodStart;

    #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
    #[Column(type: Types::DATETIME_IMMUTABLE)]
    public DateTimeImmutable $currentPeriodEnd;

    #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    public ?DateTimeImmutable $canceledAt = null;

    #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
    #[Column(options: ['default' => false])]
    public bool $cancelAtPeriodEnd = false;

    #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
    #[Column(type: Types::DATETIME_IMMUTABLE)]
    public DateTimeImmutable $updatedAt;

    public function __construct(
        #[Id]
        #[Immutable]
        #[Column(type: UuidType::NAME, unique: true)]
        public Uuid $id,

        #[Immutable]
        #[ManyToOne(targetEntity: User::class)]
        #[JoinColumn(nullable: false)]
        public User $user,

        #[Immutable]
        #[Column(length: 255, unique: true)]
        public string $stripeSubscriptionId,

        #[Immutable]
        #[Column(length: 255)]
        public string $stripeCustomerId,

        #[Column(length: 100, nullable: true)]
        public ?string $planId,

        DateTimeImmutable $currentPeriodStart,
        DateTimeImmutable $currentPeriodEnd,
        string $status,

        #[Immutable]
        #[Column(type: Types::DATETIME_IMMUTABLE)]
        public DateTimeImmutable $createdAt,
    ) {
        $this->status = $status;
        $this->currentPeriodStart = $currentPeriodStart;
        $this->currentPeriodEnd = $currentPeriodEnd;
        $this->updatedAt = $createdAt;
    }

    public function updateFromStripe(
        string $status,
        DateTimeImmutable $currentPeriodStart,
        DateTimeImmutable $currentPeriodEnd,
        ?DateTimeImmutable $canceledAt,
        bool $cancelAtPeriodEnd,
        DateTimeImmutable $now,
    ): void {
        $this->status = $status;
        $this->currentPeriodStart = $currentPeriodStart;
        $this->currentPeriodEnd = $currentPeriodEnd;
        $this->canceledAt = $canceledAt;
        $this->cancelAtPeriodEnd = $cancelAtPeriodEnd;
        $this->updatedAt = $now;
    }

    public function isActive(DateTimeImmutable $now): bool
    {
        // Active if status is active/trialing AND we're within the current period
        if (!in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_TRIALING], true)) {
            return false;
        }

        return $this->currentPeriodEnd > $now;
    }
}
