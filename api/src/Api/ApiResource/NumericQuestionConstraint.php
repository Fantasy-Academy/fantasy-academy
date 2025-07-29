<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

readonly final class NumericQuestionConstraint
{
    public function __construct(
        public int|null $min,
        public int|null $max,
    ) {}
}
