<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

readonly final class ChallengeSkillDistribution
{
    public function __construct(
        public string $name,
        public float $percentage,
    ) {
    }
}
