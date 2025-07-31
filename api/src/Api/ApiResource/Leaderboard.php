<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;

#[ApiResource(
    shortName: 'Leaderboards',
)]
#[GetCollection]
final class Leaderboard
{
    /**
     * @param array<Skill> $skills
     */
    public function __construct(
        public string $playerId,
        public string $playerName,
        public int $rank,
        public int $points,
        public int $challengesCompleted,
        public array $skills,
    ) {
    }
}
