<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;

#[ApiResource]
#[GetCollection]
final class Leaderboard
{
    public function __construct(
        public string $playerId,
        public string $playerName,
        public int $rank,
        public int $roundsPlayed,
        public int $overallPlacement
    ) {
    }
}
