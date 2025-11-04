<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ChallengeDetail;

use DateTimeImmutable;
use FantasyAcademy\API\Api\Shared\AnswerWithTexts;
use FantasyAcademy\API\Doctrine\AnswerDoctrineType;
use FantasyAcademy\API\Doctrine\ChoiceQuestionConstraintDoctrineType;
use FantasyAcademy\API\Doctrine\NumericQuestionConstraintDoctrineType;
use FantasyAcademy\API\Value\ChoiceQuestionConstraint;
use FantasyAcademy\API\Value\NumericQuestionConstraint;
use FantasyAcademy\API\Value\QuestionStatistics;
use FantasyAcademy\API\Value\QuestionType;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type NumericQuestionConstraintArray from NumericQuestionConstraintDoctrineType
 * @phpstan-import-type ChoiceQuestionConstraintArray from ChoiceQuestionConstraintDoctrineType
 * @phpstan-type QuestionRow array{
 *     id: string,
 *     text: string,
 *     type: string,
 *     image: null|string,
 *     numeric_constraint: null|string,
 *     choice_constraint: null|string,
 *     correct_answer: null|string,
 *     challenge_id: string,
 *     answered_at: null|string,
 *     text_answer: null|string,
 *     numeric_answer: null|string,
 *     selected_choice_id: null|string,
 *     selected_choice_ids: null|string,
 *     ordered_choice_ids: null|string,
 * }
 */
readonly final class QuestionResponse
{
    public function __construct(
        public Uuid $id,
        public string $text,
        public QuestionType $type,
        public null|string $image,
        public null|NumericQuestionConstraint $numericConstraint,
        public null|ChoiceQuestionConstraint $choiceConstraint,
        public null|DateTimeImmutable $answeredAt,
        public null|AnswerWithTexts $myAnswer,
        public null|AnswerWithTexts $correctAnswer,
        public null|QuestionStatistics $statistics = null,
    ) {}

    /**
     * @param QuestionRow $row
     */
    public static function fromArray(array $row): self
    {
        $numericConstraint = null;
        $choiceConstraint = null;

        if (json_validate($row['numeric_constraint'] ?? '')) {
            /** @var NumericQuestionConstraintArray $numericConstraintData */
            $numericConstraintData = json_decode($row['numeric_constraint'], associative: true);
            $numericConstraint = NumericQuestionConstraintDoctrineType::createNumericQuestionConstraintFromArray($numericConstraintData);
        }

        if (json_validate($row['choice_constraint'] ?? '')) {
            /** @var ChoiceQuestionConstraintArray $choiceConstraintData */
            $choiceConstraintData = json_decode($row['choice_constraint'], associative: true);
            $choiceConstraint = ChoiceQuestionConstraintDoctrineType::createChoiceQuestionConstraintFromArray($choiceConstraintData);
        }

        // Build choice text mapping for answer population
        $choiceTextMap = self::buildChoiceTextMap($row['choice_constraint']);

        $myAnswer = null;
        if ($row['answered_at'] !== null) {
            // Transform QuestionRow data to AnswerRow format
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

            $myAnswer = self::createAnswerWithTexts(
                textAnswer: $row['text_answer'],
                numericAnswer: $row['numeric_answer'],
                selectedChoiceId: $row['selected_choice_id'],
                selectedChoiceIds: $selectedChoiceIds,
                orderedChoiceIds: $orderedChoiceIds,
                choiceTextMap: $choiceTextMap,
            );
        }

        $correctAnswer = null;
        if (is_string($row['correct_answer']) && json_validate($row['correct_answer'])) {
            /** @var array{text_answer: null|string, numeric_answer: null|string, selected_choice_id: null|string, selected_choice_ids: null|array<string>, ordered_choice_ids: null|array<string>} $correctAnswerData */
            $correctAnswerData = json_decode($row['correct_answer'], associative: true);

            // Parse choice arrays if they exist
            $correctSelectedChoiceIds = $correctAnswerData['selected_choice_ids'] ?? null;
            $correctOrderedChoiceIds = $correctAnswerData['ordered_choice_ids'] ?? null;

            $correctAnswer = self::createAnswerWithTexts(
                textAnswer: $correctAnswerData['text_answer'],
                numericAnswer: $correctAnswerData['numeric_answer'],
                selectedChoiceId: $correctAnswerData['selected_choice_id'],
                selectedChoiceIds: $correctSelectedChoiceIds,
                orderedChoiceIds: $correctOrderedChoiceIds,
                choiceTextMap: $choiceTextMap,
            );
        }

        return new self(
            id: Uuid::fromString($row['id']),
            text: $row['text'],
            type: QuestionType::from($row['type']),
            image: $row['image'],
            numericConstraint: $numericConstraint,
            choiceConstraint: $choiceConstraint,
            answeredAt: $row['answered_at'] !== null ? new DateTimeImmutable($row['answered_at']) : null,
            myAnswer: $myAnswer,
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
     * Create AnswerWithTexts from answer data and choice text mapping.
     *
     * @param null|array<string> $selectedChoiceIds
     * @param null|array<string> $orderedChoiceIds
     * @param array<string, string> $choiceTextMap
     */
    private static function createAnswerWithTexts(
        null|string $textAnswer,
        null|string $numericAnswer,
        null|string $selectedChoiceId,
        null|array $selectedChoiceIds,
        null|array $orderedChoiceIds,
        array $choiceTextMap,
    ): AnswerWithTexts {
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
        if ($selectedChoiceId !== null) {
            $selectedChoiceIdUuid = Uuid::fromString($selectedChoiceId);
            $selectedChoiceText = $choiceTextMap[$selectedChoiceId] ?? null;
        }

        $numericAnswerFloat = null;
        if ($numericAnswer !== null) {
            $numericAnswerFloat = (float) $numericAnswer;
        }

        return new AnswerWithTexts(
            textAnswer: $textAnswer,
            numericAnswer: $numericAnswerFloat,
            selectedChoiceId: $selectedChoiceIdUuid,
            selectedChoiceText: $selectedChoiceText,
            selectedChoiceIds: $selectedChoiceIdUuids,
            selectedChoiceTexts: $selectedChoiceTexts,
            orderedChoiceIds: $orderedChoiceIdUuids,
            orderedChoiceTexts: $orderedChoiceTexts,
        );
    }
}
