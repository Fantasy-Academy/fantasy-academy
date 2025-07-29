<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

readonly final class Skill
{
    public function __construct(
        public string $name,
        public int $percentage,
        public null|int $percentageChange,
    ) {
    }
}
