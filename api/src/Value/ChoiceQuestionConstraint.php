<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

/**
 * @phpstan-import-type ChoiceArray from Choice
 * @phpstan-type ChoiceQuestionConstraintArray array{
 *     choices: array<ChoiceArray>,
 *     min_selections: null|int,
 *     max_selections: null|int,
 * }
 */
readonly final class ChoiceQuestionConstraint
{
    /**
     * @param array<Choice> $choices
     */
    public function __construct(
        public array $choices,
        public null|int $minSelections = null,
        public null|int $maxSelections = null,
    ) {}

    /**
     * @param ChoiceQuestionConstraintArray $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            choices: array_map(
                callback: fn (array $choiceData): Choice => Choice::fromArray($choiceData),
                array: $data['choices'],
            ),
            minSelections: $data['min_selections'],
            maxSelections: $data['max_selections'],
        );
    }

    /**
     * @return ChoiceQuestionConstraintArray
     */
    public function toArray(): array
    {
        return [
            'choices' => array_map(
                callback: fn (Choice $choice): array => $choice->toArray(),
                array: $this->choices,
            ),
            'min_selections' => $this->minSelections,
            'max_selections' => $this->maxSelections,
        ];
    }
}
