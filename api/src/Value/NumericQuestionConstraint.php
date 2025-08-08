<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

/**
 * @phpstan-type NumericQuestionConstraintArray array{
 *     min: null|int,
 *     max: null|int,
 * }
 */
readonly final class NumericQuestionConstraint
{
    public function __construct(
        public int|null $min,
        public int|null $max,
    ) {}

    /**
     * @param NumericQuestionConstraintArray $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            min: $data['min'],
            max: $data['max'],
        );
    }

    /**
     * @return NumericQuestionConstraintArray
     */
    public function toArray(): array
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
        ];
    }
}
