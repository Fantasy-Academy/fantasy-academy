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
        public string $name,

        #[Column(type: Types::TEXT)]
        public string $shortDescription,

        #[Column(type: Types::TEXT)]
        public string $description,

        #[Column(nullable: true)]
        public null|string $image,

        #[Column]
        readonly public DateTimeImmutable $addedAt,

        #[Column]
        public DateTimeImmutable $startsAt,

        #[Column]
        public DateTimeImmutable $expiresAt,

        #[Column]
        public int $maxPoints,

        #[Column(type: Types::TEXT, nullable: true)]
        public null|string $hintText,

        #[Column(nullable: true)]
        public null|string $hintImage,

        #[Column]
        public float $skillAnalytical,

        #[Column]
        public float $skillStrategicPlanning,

        #[Column]
        public float $skillAdaptability,

        #[Column]
        public float $skillPremierLeagueKnowledge,

        #[Column]
        public float $skillRiskManagement,

        #[Column]
        public float $skillDecisionMakingUnderPressure,

        #[Column]
        public float $skillFinancialManagement,

        #[Column]
        public float $skillLongTermVision,

        #[Column(options: ['default' => true])]
        public bool $showStatisticsContinuously = true,
    ) {
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
    }
}
