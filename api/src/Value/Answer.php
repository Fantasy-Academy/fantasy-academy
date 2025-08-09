<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type AnswerRow array{
 *     text_answer: null|string,
 *     numeric_answer: null|string,
 *     selected_choice_id: null|string,
 *     selected_choice_ids: null|string,
 *     ordered_choice_ids: null|string,
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

        if (json_validate($data['selected_choice_ids'] ?? '')) {
            /** @var array<string> $selectedChoiceIdsData */
            $selectedChoiceIdsData = json_decode($data['selected_choice_ids'], associative: true);
            $selectedChoiceIds = array_map(
                callback: fn (string $uuid): Uuid => Uuid::fromString($uuid),
                array: $selectedChoiceIdsData,
            );
        }

        if (json_validate($data['ordered_choice_ids'] ?? '')) {
            /** @var array<string> $orderedChoiceIdsData */
            $orderedChoiceIdsData = json_decode($data['ordered_choice_ids'], associative: true);
            $selectedChoiceIds = array_map(
                callback: fn (string $uuid): Uuid => Uuid::fromString($uuid),
                array: $orderedChoiceIdsData,
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
}
