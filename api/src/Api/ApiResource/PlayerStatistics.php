<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

readonly final class PlayerStatistics
{
    /**
     * @param array<Skill> $skills
     */
    public function __construct(
        public int $rank,
        public int $challengesAnswered,
        public int $points,
        public array $skills,
    ) {
    }
}
