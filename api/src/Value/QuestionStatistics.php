<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

/**
 * Collection of answer statistics for a question
 */
readonly final class QuestionStatistics
{
    /**
     * @param array<AnswerStatistic> $answers
     */
    public function __construct(
        public int $totalAnswers,
        public array $answers,
    ) {}
}
