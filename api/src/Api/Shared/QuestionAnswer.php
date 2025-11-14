<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Shared;

use Symfony\Component\Uid\Uuid;

readonly final class QuestionAnswer
{
    public function __construct(
        public Uuid $questionId,
        public string $questionText,
        public AnswerWithTexts $answer,
    ) {
    }

    /**
     * @param array{
     *     question_id: string,
     *     question_text: string,
     *     text_answer: null|string,
     *     numeric_answer: null|string,
     *     selected_choice_id: null|string,
     *     selected_choice_ids: null|string,
     *     ordered_choice_ids: null|string,
     *     choice_constraint: null|string,
     * } $row
     */
    public static function fromArray(array $row): self
    {
        $selectedChoiceIds = null;
        $orderedChoiceIds = null;

        if (is_string($row['selected_choice_ids']) && json_validate($row['selected_choice_ids'])) {
            /** @var null|array<string> $decoded */
            $decoded = json_decode($row['selected_choice_ids'], associative: true);
            $selectedChoiceIds = $decoded;
        }

        if (is_string($row['ordered_choice_ids']) && json_validate($row['ordered_choice_ids'])) {
            /** @var null|array<string> $decoded */
            $decoded = json_decode($row['ordered_choice_ids'], associative: true);
            $orderedChoiceIds = $decoded;
        }

        // Parse choice constraint to build ID -> text mapping
        $choiceTextMap = self::buildChoiceTextMap($row['choice_constraint']);

        // Convert string IDs to UUIDs and build text arrays
        $selectedChoiceIdUuids = null;
        $selectedChoiceTexts = null;
        if ($selectedChoiceIds !== null) {
            $selectedChoiceIdUuids = array_map(
                static fn (string $id): Uuid => Uuid::fromString($id),
                $selectedChoiceIds,
            );
            $selectedChoiceTexts = array_map(
                static fn (string $id): string => $choiceTextMap[$id] ?? '',
                $selectedChoiceIds,
            );
        }

        $orderedChoiceIdUuids = null;
        $orderedChoiceTexts = null;
        if ($orderedChoiceIds !== null) {
            $orderedChoiceIdUuids = array_map(
                static fn (string $id): Uuid => Uuid::fromString($id),
                $orderedChoiceIds,
            );
            $orderedChoiceTexts = array_map(
                static fn (string $id): string => $choiceTextMap[$id] ?? '',
                $orderedChoiceIds,
            );
        }

        $selectedChoiceIdUuid = null;
        $selectedChoiceText = null;
        if ($row['selected_choice_id'] !== null) {
            $selectedChoiceIdUuid = Uuid::fromString($row['selected_choice_id']);
            $selectedChoiceText = $choiceTextMap[$row['selected_choice_id']] ?? null;
        }

        $numericAnswer = null;
        if ($row['numeric_answer'] !== null) {
            $numericAnswer = (float) $row['numeric_answer'];
        }

        return new self(
            questionId: Uuid::fromString($row['question_id']),
            questionText: $row['question_text'],
            answer: new AnswerWithTexts(
                textAnswer: $row['text_answer'],
                numericAnswer: $numericAnswer,
                selectedChoiceId: $selectedChoiceIdUuid,
                selectedChoiceText: $selectedChoiceText,
                selectedChoiceIds: $selectedChoiceIdUuids,
                selectedChoiceTexts: $selectedChoiceTexts,
                orderedChoiceIds: $orderedChoiceIdUuids,
                orderedChoiceTexts: $orderedChoiceTexts,
            ),
        );
    }

    /**
     * Build a mapping of choice ID (string) to choice text from choice_constraint JSONB.
     *
     * @return array<string, string>
     */
    private static function buildChoiceTextMap(null|string $choiceConstraintJson): array
    {
        if ($choiceConstraintJson === null || !json_validate($choiceConstraintJson)) {
            return [];
        }

        /** @var null|array{choices?: array<array{id?: string, text?: string}>} $choiceConstraint */
        $choiceConstraint = json_decode($choiceConstraintJson, associative: true);

        if ($choiceConstraint === null || !isset($choiceConstraint['choices'])) {
            return [];
        }

        $map = [];
        foreach ($choiceConstraint['choices'] as $choice) {
            if (isset($choice['id'], $choice['text'])) {
                $map[$choice['id']] = $choice['text'];
            }
        }

        return $map;
    }
}
