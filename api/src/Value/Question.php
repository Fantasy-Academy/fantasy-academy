<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

use DateTimeImmutable;
use FantasyAcademy\API\Doctrine\AnswerDoctrineType;
use FantasyAcademy\API\Doctrine\ChoiceQuestionConstraintDoctrineType;
use FantasyAcademy\API\Doctrine\NumericQuestionConstraintDoctrineType;
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
 *     challenge_id: string,
 *     answered_at: null|string,
 *     text_answer: null|string,
 *     numeric_answer: null|string,
 *     selected_choice_id: null|string,
 *     selected_choice_ids: null|string,
 *     ordered_choice_ids: null|string,
 * }
 */
readonly final class Question
{
    public function __construct(
        public Uuid $id,
        public string $text,
        public QuestionType $type,
        public null|string $image,
        public null|NumericQuestionConstraint $numericConstraint,
        public null|ChoiceQuestionConstraint $choiceConstraint,
        public null|DateTimeImmutable $answeredAt,
        public null|Answer $myAnswer,
        public null|Answer $correctAnswer,
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

            $answerData = [
                'text_answer' => $row['text_answer'],
                'numeric_answer' => $row['numeric_answer'],
                'selected_choice_id' => $row['selected_choice_id'],
                'selected_choice_ids' => $selectedChoiceIds,
                'ordered_choice_ids' => $orderedChoiceIds,
            ];
            $myAnswer = AnswerDoctrineType::createAnswerFromArray($answerData);
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
            correctAnswer: null,
        );
    }
}
