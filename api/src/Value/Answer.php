<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type AnswerRow array{
 *     text_answer: null|string,
 *     numeric_answer: null|string,
 *     selected_choice_id: null|string,
 *     selected_choice_ids: null|array<string>,
 *     ordered_choice_ids: null|array<string>,
 * }
 */
readonly final class Answer
{
    /**
     * @param null|array<Uuid> $selectedChoiceIds
     * @param null|array<Uuid> $orderedChoiceIds
     */
    public function __construct(
        public null|string $textAnswer = null,
        public null|float $numericAnswer = null,
        public null|Uuid $selectedChoiceId = null,
        public null|array $selectedChoiceIds = null,
        public null|array $orderedChoiceIds = null,
    ) {}

    /**
     * @param AnswerRow $data
     */
    public static function fromArray(array $data): self
    {
        $selectedChoiceIds = null;
        $orderedChoiceIds = null;

        if (is_array($data['selected_choice_ids'] ?? null)) {
            $selectedChoiceIds = array_map(
                callback: fn (string $uuid): Uuid => Uuid::fromString($uuid),
                array: $data['selected_choice_ids'],
            );
        }

        if (is_array($data['ordered_choice_ids'] ?? null)) {
            $orderedChoiceIds = array_map(
                callback: fn (string $uuid): Uuid => Uuid::fromString($uuid),
                array: $data['ordered_choice_ids'],
            );
        }

        return new self(
            textAnswer: $data["text_answer"],
            numericAnswer: $data["numeric_answer"] !== null ? (float) $data["numeric_answer"] : null,
            selectedChoiceId: $data["selected_choice_id"] !== null ? Uuid::fromString($data["selected_choice_id"]) : null,
            selectedChoiceIds: $selectedChoiceIds,
            orderedChoiceIds: $orderedChoiceIds,
        );
    }

    /**
     * @return AnswerRow
     */
    public function toArray(): array
    {
        $selectedChoiceIds = null;
        $orderedChoiceIds = null;

        if ($this->selectedChoiceIds !== null) {
            $selectedChoiceIds = array_map(
                callback: fn (Uuid $uuid): string => $uuid->toString(),
                array: $this->selectedChoiceIds,
            );
        }

        if ($this->orderedChoiceIds !== null) {
            $orderedChoiceIds = array_map(
                callback: fn (Uuid $uuid): string => $uuid->toString(),
                array: $this->orderedChoiceIds,
            );
        }

        return [
            'text_answer' => $this->textAnswer,
            'numeric_answer' => $this->numericAnswer !== null ? (string) $this->numericAnswer : null,
            'selected_choice_id' => $this->selectedChoiceId?->toString(),
            'selected_choice_ids' => $selectedChoiceIds,
            'ordered_choice_ids' => $orderedChoiceIds,
        ];
    }
}
