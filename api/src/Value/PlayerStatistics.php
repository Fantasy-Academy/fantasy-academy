<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

readonly final class PlayerStatistics
{
    /**
     * @param array<PlayerSkill> $skills
     */
    public function __construct(
        public null|int $rank,
        public int $challengesAnswered,
        public int $points,
        public array $skills,
        public int $rankChange = 0,
        public int $pointsChange = 0,
    ) {
    }
}
