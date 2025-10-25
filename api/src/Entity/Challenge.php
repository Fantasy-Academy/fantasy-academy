<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use JetBrains\PhpStorm\Immutable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class Challenge
{
    #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
    #[Column(nullable: true)]
    public null|DateTimeImmutable $evaluatedAt = null;

    public function __construct(
        #[Id]
        #[Immutable]
        #[Column(type: UuidType::NAME, unique: true)]
        public Uuid $id,

        #[Column(type: Types::TEXT)]
        readonly public string $name,

        #[Column(type: Types::TEXT)]
        readonly public string $shortDescription,

        #[Column(type: Types::TEXT)]
        readonly public string $description,

        #[Column(nullable: true)]
        readonly public null|string $image,

        #[Column]
        readonly public DateTimeImmutable $addedAt,

        #[Column]
        readonly public DateTimeImmutable $startsAt,

        #[Column]
        readonly public DateTimeImmutable $expiresAt,

        #[Column]
        readonly public int $maxPoints,

        #[Column(type: Types::TEXT, nullable: true)]
        readonly public null|string $hintText,

        #[Column(nullable: true)]
        readonly public null|string $hintImage,

        #[Column]
        readonly public float $skillAnalytical,

        #[Column]
        readonly public float $skillStrategicPlanning,

        #[Column]
        readonly public float $skillAdaptability,

        #[Column]
        readonly public float $skillPremierLeagueKnowledge,

        #[Column]
        readonly public float $skillRiskManagement,

        #[Column]
        readonly public float $skillDecisionMakingUnderPressure,

        #[Column]
        readonly public float $skillFinancialManagement,

        #[Column]
        readonly public float $skillLongTermVision,

        #[Column]
        readonly public bool $showStatisticsContinuously = true,
    ) {
    }

    public function evaluate(DateTimeImmutable $evaluatedAt): void
    {
        $this->evaluatedAt = $evaluatedAt;
    }
}
