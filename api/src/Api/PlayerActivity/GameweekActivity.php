<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\PlayerActivity;

readonly final class GameweekActivity
{
    public function __construct(
        public int $gameweek,
        public int $totalChallenges,
        public int $answeredChallenges,
        public float $activity,
        public int $pointsEarned,
        public int $maxPointsPossible,
    ) {
    }
}
