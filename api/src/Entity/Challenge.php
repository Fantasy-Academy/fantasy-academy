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

        #[Column(type: Types::DATETIME_IMMUTABLE)]
        readonly public DateTimeImmutable $startsAt,

        #[Column(type: Types::DATETIME_IMMUTABLE)]
        readonly public DateTimeImmutable $expiresAt,

        #[Column]
        readonly public int $maxPoints,

        #[Column(type: Types::TEXT, nullable: true)]
        readonly public null|string $hintText,

        #[Column(nullable: true)]
        readonly public null|string $hintImage,

        #[Column]
        readonly public int $skillAnalytical,

        #[Column]
        readonly public int $skillStrategicPlanning,

        #[Column]
        readonly public int $skillAdaptability,

        #[Column]
        readonly public int $skillPremierLeagueKnowledge,

        #[Column]
        readonly public int $skillRiskManagement,

        #[Column]
        readonly public int $skillDecisionMakingUnderPressure,

        #[Column]
        readonly public int $skillFinancialManagement,

        #[Column]
        readonly public int $skillLongTermVision,

        #[Column]
        readonly public int $skillDiscipline,
    ) {
    }
}
