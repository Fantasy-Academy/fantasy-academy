<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

/**
 * @phpstan-import-type ChoiceArray from Choice
 * @phpstan-type ChoiceQuestionConstraintArray array{
 *     choices: array<ChoiceArray>,
 *     minSelections: null|int,
 *     maxSelections: null|int,
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
            minSelections: $data['minSelections'],
            maxSelections: $data['maxSelections'],
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
            'minSelections' => $this->minSelections,
            'maxSelections' => $this->maxSelections,
        ];
    }
}
