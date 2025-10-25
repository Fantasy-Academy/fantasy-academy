<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

/**
 * Represents statistics for a specific answer variant
 */
readonly final class AnswerStatistic
{
    public function __construct(
        public string $answer,
        public int $count,
        public float $percentage,
    ) {}
}
