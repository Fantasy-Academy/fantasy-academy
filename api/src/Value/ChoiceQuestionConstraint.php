<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

readonly final class ChoiceQuestionConstraint
{
    /**
     * @param array<Choice> $choices
     */
    public function __construct(
        public array $choices,
        public null|int $minSelections,
        public null|int $maxSelections,
    ) {}
}
