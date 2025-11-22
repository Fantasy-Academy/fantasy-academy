<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Shared;

use Symfony\Component\Uid\Uuid;

readonly final class QuestionAnswerWithCorrect
{
    public function __construct(
        public Uuid $questionId,
        public string $questionText,
        public AnswerWithTexts $answer,
        public null|AnswerWithTexts $correctAnswer,
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
     *     correct_answer: null|string,
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

        // Build correct answer from the correct_answer JSONB column
        $correctAnswer = self::buildCorrectAnswer($row['correct_answer'], $choiceTextMap);

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
            correctAnswer: $correctAnswer,
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

    /**
     * Build AnswerWithTexts from the correct_answer JSONB column.
     *
     * @param array<string, string> $choiceTextMap
     */
    private static function buildCorrectAnswer(null|string $correctAnswerJson, array $choiceTextMap): null|AnswerWithTexts
    {
        if ($correctAnswerJson === null || !json_validate($correctAnswerJson)) {
            return null;
        }

        /** @var null|array{text_answer?: string, numeric_answer?: float|int|string, selected_choice_id?: string, selected_choice_ids?: array<string>, ordered_choice_ids?: array<string>} $correctAnswer */
        $correctAnswer = json_decode($correctAnswerJson, associative: true);

        if ($correctAnswer === null) {
            return null;
        }

        $textAnswer = $correctAnswer['text_answer'] ?? null;
        $numericAnswer = isset($correctAnswer['numeric_answer']) ? (float) $correctAnswer['numeric_answer'] : null;

        $selectedChoiceId = null;
        $selectedChoiceText = null;
        if (isset($correctAnswer['selected_choice_id'])) {
            $selectedChoiceId = Uuid::fromString($correctAnswer['selected_choice_id']);
            $selectedChoiceText = $choiceTextMap[$correctAnswer['selected_choice_id']] ?? null;
        }

        $selectedChoiceIds = null;
        $selectedChoiceTexts = null;
        if (isset($correctAnswer['selected_choice_ids'])) {
            $selectedChoiceIds = array_map(
                static fn (string $id): Uuid => Uuid::fromString($id),
                $correctAnswer['selected_choice_ids'],
            );
            $selectedChoiceTexts = array_map(
                static fn (string $id): string => $choiceTextMap[$id] ?? '',
                $correctAnswer['selected_choice_ids'],
            );
        }

        $orderedChoiceIds = null;
        $orderedChoiceTexts = null;
        if (isset($correctAnswer['ordered_choice_ids'])) {
            $orderedChoiceIds = array_map(
                static fn (string $id): Uuid => Uuid::fromString($id),
                $correctAnswer['ordered_choice_ids'],
            );
            $orderedChoiceTexts = array_map(
                static fn (string $id): string => $choiceTextMap[$id] ?? '',
                $correctAnswer['ordered_choice_ids'],
            );
        }

        return new AnswerWithTexts(
            textAnswer: $textAnswer,
            numericAnswer: $numericAnswer,
            selectedChoiceId: $selectedChoiceId,
            selectedChoiceText: $selectedChoiceText,
            selectedChoiceIds: $selectedChoiceIds,
            selectedChoiceTexts: $selectedChoiceTexts,
            orderedChoiceIds: $orderedChoiceIds,
            orderedChoiceTexts: $orderedChoiceTexts,
        );
    }
}
