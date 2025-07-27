<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use DateTimeImmutable;

#[ApiResource]
#[GetCollection]
readonly final class Challenge
{
    public function __construct(
        public string $name,
        public DateTimeImmutable $addedAt,
        public DateTimeImmutable $expiresAt,
        public null|DateTimeImmutable $completedAt,
    ) {
    }
}
