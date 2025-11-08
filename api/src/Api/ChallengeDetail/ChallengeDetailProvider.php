<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ChallengeDetail;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\DBAL\Connection;
use FantasyAcademy\API\Doctrine\AnswerDoctrineType;
use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Exceptions\ChallengeNotFound;
use FantasyAcademy\API\Value\AnswerStatistic;
use FantasyAcademy\API\Value\QuestionStatistics;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type ChallengeDetailResponseRow from ChallengeDetailResponse
 * @phpstan-import-type QuestionRow from QuestionResponse
 *
 * @implements ProviderInterface<ChallengeDetailResponse>
 */
readonly final class ChallengeDetailProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private Connection $database,
        private ClockInterface $clock,
    ) {}

    /**
     * @param array{id?: Uuid} $uriVariables
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ChallengeDetailResponse
    {
        /** @var null|User $user */
        $user = $this->security->getUser();

        assert(isset($uriVariables['id']));
        $challengeId = $uriVariables['id'];

        return $this->getChallengeDetail($challengeId, $user?->id);
    }

    private function getChallengeDetail(Uuid $challengeId, null|Uuid $userId): ChallengeDetailResponse
    {
        $query = <<<SQL
SELECT challenge.*, player_challenge_answer.answered_at, player_challenge_answer.points AS my_points
FROM challenge
LEFT JOIN player_challenge_answer ON challenge.id = player_challenge_answer.challenge_id AND player_challenge_answer.user_id = :userId
WHERE challenge.id = :challengeId
SQL;

        /** @var false|ChallengeDetailResponseRow $row */
        $row = $this->database
            ->executeQuery($query, [
                'challengeId' => $challengeId->toString(),
                'userId' => $userId?->toString(),
            ])
            ->fetchAssociative();

        if ($row === false) {
            throw new ChallengeNotFound();
        }

        // Determine if statistics should be shown
        $isEvaluated = $row['evaluated_at'] !== null;
        $showStatisticsContinuously = (bool) $row['show_statistics_continuously'];
        $shouldShowStatistics = $isEvaluated || $showStatisticsContinuously;

        return ChallengeDetailResponse::fromArray(
            $row,
            $this->clock->now(),
            $this->getQuestions($challengeId, $userId, $shouldShowStatistics),
        );
    }

    /**
     * @return array<QuestionResponse>
     */
    private function getQuestions(Uuid $challengeId, null|Uuid $userId, bool $shouldShowStatistics): array
    {
        $query = <<<SQL
SELECT question.*, player_answered_question.*
FROM question
LEFT JOIN player_challenge_answer ON question.challenge_id = player_challenge_answer.challenge_id AND player_challenge_answer.user_id = :userId
LEFT JOIN player_answered_question ON player_answered_question.challenge_answer_id = player_challenge_answer.id AND player_answered_question.question_id = question.id
WHERE question.challenge_id = :challengeId
SQL;

        /** @var array<QuestionRow> $rows */
        $rows = $this->database
            ->executeQuery($query, [
                'challengeId' => $challengeId->toString(),
                'userId' => $userId?->toString(),
            ])
            ->fetchAllAssociative();

        // Fetch all statistics at once if needed
        $statisticsMap = $shouldShowStatistics ? $this->getAllQuestionStatistics($challengeId) : [];

        $questions = [];
        foreach ($rows as $row) {
            $questionId = Uuid::fromString($row['id']);
            $statistics = $statisticsMap[$questionId->toString()] ?? null;

            $question = QuestionResponse::fromArray($row);

            // We need to create a new Question with statistics
            $questions[] = new QuestionResponse(
                id: $question->id,
                text: $question->text,
                type: $question->type,
                image: $question->image,
                numericConstraint: $question->numericConstraint,
                choiceConstraint: $question->choiceConstraint,
                answeredAt: $question->answeredAt,
                myAnswer: $question->myAnswer,
                correctAnswer: $question->correctAnswer,
                statistics: $statistics,
            );
        }

        return $questions;
    }

    /**
     * Fetch statistics for all questions in a challenge at once.
     *
     * @return array<string, QuestionStatistics> Map of questionId => QuestionStatistics
     */
    private function getAllQuestionStatistics(Uuid $challengeId): array
    {
        // Get all questions with their choice_constraints
        $questionsQuery = <<<SQL
SELECT id, choice_constraint
FROM question
WHERE challenge_id = :challengeId
SQL;

        /** @var array<array{id: string, choice_constraint: null|string}> $questionRows */
        $questionRows = $this->database
            ->executeQuery($questionsQuery, [
                'challengeId' => $challengeId->toString(),
            ])
            ->fetchAllAssociative();

        // Build choice text maps for each question
        $choiceTextMaps = [];
        foreach ($questionRows as $questionRow) {
            $questionId = $questionRow['id'];
            $choiceTextMaps[$questionId] = [];
            if ($questionRow['choice_constraint'] !== null) {
                $choiceTextMaps[$questionId] = $this->buildChoiceTextMap($questionRow['choice_constraint']);
            }
        }

        // Get all answer statistics for all questions in this challenge
        $query = <<<SQL
SELECT
    paq.question_id,
    COALESCE(paq.text_answer, '') as text_answer,
    COALESCE(CAST(paq.numeric_answer AS TEXT), '') as numeric_answer,
    COALESCE(CAST(paq.selected_choice_id AS TEXT), '') as selected_choice_id,
    COALESCE(CAST(paq.selected_choice_ids AS TEXT), '') as selected_choice_ids,
    COALESCE(CAST(paq.ordered_choice_ids AS TEXT), '') as ordered_choice_ids,
    COUNT(*) as answer_count
FROM player_answered_question paq
JOIN player_challenge_answer pca ON paq.challenge_answer_id = pca.id
WHERE pca.challenge_id = :challengeId
GROUP BY
    paq.question_id,
    paq.text_answer,
    paq.numeric_answer,
    paq.selected_choice_id,
    paq.selected_choice_ids,
    paq.ordered_choice_ids
SQL;

        /** @var array<array{question_id: string, text_answer: string, numeric_answer: string, selected_choice_id: string, selected_choice_ids: string, ordered_choice_ids: string, answer_count: int}> $results */
        $results = $this->database
            ->executeQuery($query, [
                'challengeId' => $challengeId->toString(),
            ])
            ->fetchAllAssociative();

        // Group results by question_id
        $resultsByQuestion = [];
        foreach ($results as $result) {
            $questionId = $result['question_id'];
            if (!isset($resultsByQuestion[$questionId])) {
                $resultsByQuestion[$questionId] = [];
            }
            $resultsByQuestion[$questionId][] = $result;
        }

        // Build QuestionStatistics for each question
        $statisticsMap = [];
        foreach ($questionRows as $questionRow) {
            $questionId = $questionRow['id'];
            $questionResults = $resultsByQuestion[$questionId] ?? [];

            // Calculate total answers for this question
            $totalAnswers = array_sum(array_column($questionResults, 'answer_count'));

            if ($totalAnswers === 0) {
                $statisticsMap[$questionId] = new QuestionStatistics(totalAnswers: 0, answers: []);
                continue;
            }

            // Build answer statistics
            $answerStats = [];
            $choiceTextMap = $choiceTextMaps[$questionId];

            foreach ($questionResults as $result) {
                // Parse JSON arrays for choice fields
                $selectedChoiceIds = null;
                $orderedChoiceIds = null;

                if ($result['selected_choice_ids'] !== '' && json_validate($result['selected_choice_ids'])) {
                    /** @var null|array<string> $decoded */
                    $decoded = json_decode($result['selected_choice_ids'], associative: true);
                    $selectedChoiceIds = $decoded;
                }

                if ($result['ordered_choice_ids'] !== '' && json_validate($result['ordered_choice_ids'])) {
                    /** @var null|array<string> $decoded */
                    $decoded = json_decode($result['ordered_choice_ids'], associative: true);
                    $orderedChoiceIds = $decoded;
                }

                // Construct AnswerWithTexts object
                $answer = $this->createAnswerWithTextsFromStatistics(
                    textAnswer: $result['text_answer'] !== '' ? $result['text_answer'] : null,
                    numericAnswer: $result['numeric_answer'] !== '' ? $result['numeric_answer'] : null,
                    selectedChoiceId: $result['selected_choice_id'] !== '' ? $result['selected_choice_id'] : null,
                    selectedChoiceIds: $selectedChoiceIds,
                    orderedChoiceIds: $orderedChoiceIds,
                    choiceTextMap: $choiceTextMap,
                );

                $count = (int) $result['answer_count'];
                $percentage = ($count / $totalAnswers) * 100;

                $answerStats[] = new AnswerStatistic(
                    answer: $answer,
                    count: $count,
                    percentage: $percentage,
                );
            }

            $statisticsMap[$questionId] = new QuestionStatistics(
                totalAnswers: $totalAnswers,
                answers: $answerStats,
            );
        }

        return $statisticsMap;
    }

    /**
     * Build a mapping of choice ID (string) to choice text from choice_constraint JSONB.
     *
     * @return array<string, string>
     */
    private function buildChoiceTextMap(string $choiceConstraintJson): array
    {
        if (!json_validate($choiceConstraintJson)) {
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
    private function createAnswerWithTextsFromStatistics(
        null|string $textAnswer,
        null|string|float $numericAnswer,
        null|string $selectedChoiceId,
        null|array $selectedChoiceIds,
        null|array $orderedChoiceIds,
        array $choiceTextMap,
    ): \FantasyAcademy\API\Api\Shared\AnswerWithTexts {
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

        return new \FantasyAcademy\API\Api\Shared\AnswerWithTexts(
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
