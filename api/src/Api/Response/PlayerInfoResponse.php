<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use DateTimeImmutable;
use FantasyAcademy\API\Value\PlayerSeasonStatistics;
use FantasyAcademy\API\Value\PlayerStatistics;
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
