<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use FantasyAcademy\API\Doctrine\UuidArrayDoctrineType;
use JetBrains\PhpStorm\Immutable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class PlayerAnsweredQuestion
{
    public function __construct(
        #[Id]
        #[ManyToOne]
        #[JoinColumn(nullable: false)]
        readonly public Question $question,

        #[Id]
        #[ManyToOne(inversedBy: 'answeredQuestions')]
        #[JoinColumn(nullable: false)]
        readonly public PlayerChallengeAnswer $challengeAnswer,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column]
        public DateTimeImmutable $answeredAt,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(nullable: true)]
        public null|string $textAnswer = null,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(nullable: true)]
        public null|float $numericAnswer = null,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(type: UuidType::NAME, nullable: true)]
        public null|Uuid $selectedChoiceId = null,

        /** @var null|array<Uuid> */
        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(type: UuidArrayDoctrineType::NAME, nullable: true)]
        public null|array $selectedChoiceIds = null,

        /** @var null|array<Uuid> */
        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(type: UuidArrayDoctrineType::NAME, nullable: true)]
        public null|array $orderedChoiceIds = null,
    ) {
    }

    /**
     * @param null|array<Uuid> $orderedChoiceIds
     * @param null|array<Uuid> $selectedChoiceIds
     */
    public function changeAnswer(
        DateTimeImmutable $answeredAt,
        null|string $textAnswer = null,
        null|float $numericAnswer = null,
        null|Uuid $selectedChoiceId = null,
        null|array $selectedChoiceIds = null,
        null|array $orderedChoiceIds = null,
    ): void
    {
        $this->answeredAt = $answeredAt;
        $this->textAnswer = $textAnswer;
        $this->numericAnswer = $numericAnswer;
        $this->selectedChoiceId = $selectedChoiceId;
        $this->selectedChoiceIds = $selectedChoiceIds;
        $this->orderedChoiceIds = $orderedChoiceIds;
    }
}
