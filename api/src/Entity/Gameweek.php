<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\UniqueConstraint;
use JetBrains\PhpStorm\Immutable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[UniqueConstraint(columns: ['season', 'number'])]
class Gameweek
{
    public function __construct(
        #[Id]
        #[Immutable]
        #[Column(type: UuidType::NAME, unique: true)]
        public Uuid $id,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column]
        public int $season,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column]
        public int $number,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(nullable: true)]
        public null|string $title,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(type: Types::TEXT, nullable: true)]
        public null|string $description,

        #[Column]
        readonly public DateTimeImmutable $startsAt,

        #[Column]
        readonly public DateTimeImmutable $endsAt,
    ) {
    }
}
