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

    #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
    #[Column(nullable: true)]
    public null|int $gameweek = null;

    public function __construct(
        #[Id]
        #[Immutable]
        #[Column(type: UuidType::NAME, unique: true)]
        public Uuid $id,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(type: Types::TEXT)]
        public string $name,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(type: Types::TEXT)]
        public string $shortDescription,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(type: Types::TEXT)]
        public string $description,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(nullable: true)]
        public null|string $image,

        #[Column]
        readonly public DateTimeImmutable $addedAt,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column]
        public DateTimeImmutable $startsAt,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column]
        public DateTimeImmutable $expiresAt,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column]
        public int $maxPoints,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(type: Types::TEXT, nullable: true)]
        public null|string $hintText,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(nullable: true)]
        public null|string $hintImage,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column]
        public float $skillAnalytical,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column]
        public float $skillStrategicPlanning,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column]
        public float $skillAdaptability,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column]
        public float $skillPremierLeagueKnowledge,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column]
        public float $skillRiskManagement,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column]
        public float $skillDecisionMakingUnderPressure,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column]
        public float $skillFinancialManagement,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column]
        public float $skillLongTermVision,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(options: ['default' => true])]
        public bool $showStatisticsContinuously = true,

        null|int $gameweek = null,
    ) {
        $this->gameweek = $gameweek;
    }

    public function evaluate(DateTimeImmutable $evaluatedAt): void
    {
        $this->evaluatedAt = $evaluatedAt;
    }

    public function update(
        string $name,
        string $shortDescription,
        string $description,
        null|string $image,
        DateTimeImmutable $startsAt,
        DateTimeImmutable $expiresAt,
        int $maxPoints,
        null|string $hintText,
        null|string $hintImage,
        float $skillAnalytical,
        float $skillStrategicPlanning,
        float $skillAdaptability,
        float $skillPremierLeagueKnowledge,
        float $skillRiskManagement,
        float $skillDecisionMakingUnderPressure,
        float $skillFinancialManagement,
        float $skillLongTermVision,
        bool $showStatisticsContinuously,
        null|int $gameweek,
    ): void {
        $this->name = $name;
        $this->shortDescription = $shortDescription;
        $this->description = $description;
        $this->image = $image;
        $this->startsAt = $startsAt;
        $this->expiresAt = $expiresAt;
        $this->maxPoints = $maxPoints;
        $this->hintText = $hintText;
        $this->hintImage = $hintImage;
        $this->skillAnalytical = $skillAnalytical;
        $this->skillStrategicPlanning = $skillStrategicPlanning;
        $this->skillAdaptability = $skillAdaptability;
        $this->skillPremierLeagueKnowledge = $skillPremierLeagueKnowledge;
        $this->skillRiskManagement = $skillRiskManagement;
        $this->skillDecisionMakingUnderPressure = $skillDecisionMakingUnderPressure;
        $this->skillFinancialManagement = $skillFinancialManagement;
        $this->skillLongTermVision = $skillLongTermVision;
        $this->showStatisticsContinuously = $showStatisticsContinuously;
        $this->gameweek = $gameweek;
    }
}
