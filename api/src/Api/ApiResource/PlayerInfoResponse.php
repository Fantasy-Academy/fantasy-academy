<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    shortName: 'Player info',
)]
#[Get(
    uriTemplate: '/player/{id}',
    // provider: ChallengeDetailProvider::class,
)]
readonly final class PlayerInfoResponse
{
    /**
     * @param array<PlayerSeasonStatistics> $seasonsStatistics
     */
    public function __construct(
        public Uuid $id,
        public bool $isMyself,
        public string $name,
        public DateTimeImmutable $registeredAt,
        public PlayerStatistics $overallStatistics,
        public array $seasonsStatistics,
    ) {
    }
}
