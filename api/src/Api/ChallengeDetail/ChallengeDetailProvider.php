<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ChallengeDetail;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\DBAL\Connection;
use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Exceptions\ChallengeNotFound;
use FantasyAcademy\API\Value\AnswerStatistic;
use FantasyAcademy\API\Value\Question;
use FantasyAcademy\API\Value\QuestionStatistics;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type ChallengeDetailResponseRow from ChallengeDetailResponse
 * @phpstan-import-type QuestionRow from Question
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
     * @return array<Question>
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

        $questions = [];
        foreach ($rows as $row) {
            $questionId = Uuid::fromString($row['id']);
            $statistics = $shouldShowStatistics ? $this->getQuestionStatistics($questionId) : null;

            $question = Question::fromArray($row);

            // We need to create a new Question with statistics
            $questions[] = new Question(
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

    private function getQuestionStatistics(Uuid $questionId): QuestionStatistics
    {
        // Query to get all answers for a specific question with counts
        $query = <<<SQL
SELECT
    COALESCE(paq.text_answer, '') as text_answer,
    COALESCE(CAST(paq.numeric_answer AS TEXT), '') as numeric_answer,
    COALESCE(CAST(paq.selected_choice_id AS TEXT), '') as selected_choice_id,
    COALESCE(CAST(paq.selected_choice_ids AS TEXT), '') as selected_choice_ids,
    COALESCE(CAST(paq.ordered_choice_ids AS TEXT), '') as ordered_choice_ids,
    COUNT(*) as answer_count
FROM player_answered_question paq
WHERE paq.question_id = :questionId
GROUP BY
    paq.text_answer,
    paq.numeric_answer,
    paq.selected_choice_id,
    paq.selected_choice_ids,
    paq.ordered_choice_ids
SQL;

        /** @var array<array{text_answer: string, numeric_answer: string, selected_choice_id: string, selected_choice_ids: string, ordered_choice_ids: string, answer_count: int}> $results */
        $results = $this->database
            ->executeQuery($query, [
                'questionId' => $questionId->toString(),
            ])
            ->fetchAllAssociative();

        // Calculate total answers
        $totalAnswers = array_sum(array_column($results, 'answer_count'));

        if ($totalAnswers === 0) {
            return new QuestionStatistics(totalAnswers: 0, answers: []);
        }

        // Build answer statistics
        $answerStats = [];
        foreach ($results as $result) {
            // Determine the answer representation based on which field is populated
            $answerRepresentation = '';
            if ($result['text_answer'] !== '') {
                $answerRepresentation = $result['text_answer'];
            } elseif ($result['numeric_answer'] !== '') {
                $answerRepresentation = $result['numeric_answer'];
            } elseif ($result['selected_choice_id'] !== '') {
                $answerRepresentation = $result['selected_choice_id'];
            } elseif ($result['selected_choice_ids'] !== '') {
                $answerRepresentation = $result['selected_choice_ids'];
            } elseif ($result['ordered_choice_ids'] !== '') {
                $answerRepresentation = $result['ordered_choice_ids'];
            }

            $count = (int) $result['answer_count'];
            $percentage = ($count / $totalAnswers) * 100;

            $answerStats[] = new AnswerStatistic(
                answer: $answerRepresentation,
                count: $count,
                percentage: $percentage,
            );
        }

        return new QuestionStatistics(
            totalAnswers: $totalAnswers,
            answers: $answerStats,
        );
    }
}
