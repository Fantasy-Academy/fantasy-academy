<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

use FantasyAcademy\API\Api\Shared\AnswerWithTexts;

/**
 * Represents statistics for a specific answer variant
 */
readonly final class AnswerStatistic
{
    public function __construct(
        public AnswerWithTexts $answer,
        public int $count,
        public float $percentage,
    ) {}
}
