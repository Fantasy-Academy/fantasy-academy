<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

readonly final class PlayerSeasonStatistics
{
    /**
     * @param array<PlayerSkill> $skills
     */
    public function __construct(
        public int $seasonNumber,
        public null|int $rank,
        public int $challengesAnswered,
        public int $points,
        public array $skills,
    ) {
    }
}
