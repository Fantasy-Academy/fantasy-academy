<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

readonly final class PlayerSkill
{
    public function __construct(
        public string $name,
        public int $percentage,
        public null|int $percentageChange,
    ) {
    }
}
