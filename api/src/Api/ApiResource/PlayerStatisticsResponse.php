<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;

#[ApiResource(
    shortName: 'Player statistics',
)]
#[Get(
    uriTemplate: '/player/{id}/statistics',
    // provider: ChallengeDetailProvider::class,
)]
readonly final class PlayerStatisticsResponse
{
    /**
     * @param array<Skill> $skills
     */
    public function __construct(
        public int $seasonNumber,
        public int $availableChallenges,
        public int $completedChallenges,
        public int $points,
        public int $rank,
        public int $totalPlayersCount,
        public array $skills,
    ) {
    }
}
