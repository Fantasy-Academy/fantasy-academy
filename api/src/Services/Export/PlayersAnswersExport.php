<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Export;

use FantasyAcademy\API\Query\PlayerChallengeAnswerQuery;
use FantasyAcademy\API\Query\PlayerQuestionAnswerQuery;
use FantasyAcademy\API\Result\PlayerChallengeAnswerRow;
use FantasyAcademy\API\Result\PlayerQuestionAnswerRow;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\Uid\Uuid;

readonly final class PlayersAnswersExport
{
    public function __construct(
        private PlayerChallengeAnswerQuery $playerChallengeAnswerQuery,
        private PlayerQuestionAnswerQuery $playerQuestionAnswerQuery,
    ) {
    }

    /**
     * @param array<string> $challengeIds
     */
    public function exportAnswers(array $challengeIds): Spreadsheet
    {
        $answersData = [];
        $pointsData = [];
        $spreadsheet = new Spreadsheet();
        $pointsSheet = $spreadsheet->getActiveSheet();
        $pointsSheet->setTitle('Points');
        $pointsSheet->fromArray(['id', 'user_id', 'challenge_id', 'name', 'points']);

        foreach ($challengeIds as $challengeId) {
            $challengeAnswers = $this->playerChallengeAnswerQuery->getForChallenge(Uuid::fromString($challengeId));

            foreach ($challengeAnswers as $answer) {
                $pointsData[] = [
                    $answer->id,
                    $answer->userId,
                    $answer->challengeId,
                    $answer->name,
                    $answer->points,
                ];
            }
        }
        
        if (count($pointsData) > 0) {
            $pointsSheet->fromArray($pointsData, startCell: 'A2');
        }

        $answersSheet = $spreadsheet->createSheet();
        $answersSheet->setTitle('Answers');

        $answersSheet->fromArray([
            'player_id', 'challenge_id', 'question_id', 'question_name',
            'text_answer', 'numeric_answer', 'selected_choice_text',
            'selected_choice_texts', 'ordered_choice_texts', 'challenge_answer_id'
        ]);

        foreach ($challengeIds as $challengeId) {
            $questionAnswers = $this->playerQuestionAnswerQuery->getForChallenge(Uuid::fromString($challengeId));

            foreach ($questionAnswers as $answer) {
                $answersData[] = [
                    $answer->playerId,
                    $answer->challengeId,
                    $answer->questionId,
                    $answer->questionName,
                    $answer->textAnswer,
                    $answer->numericAnswer,
                    $answer->selectedChoiceText,
                    $answer->selectedChoiceTexts,
                    $answer->orderedChoiceTexts,
                    $answer->challengeAnswerId,
                ];
            }
        }
        
        if (count($answersData) > 0) {
            $answersSheet->fromArray($answersData, startCell: 'A2');
        }
        
        return $spreadsheet;
    }
}
