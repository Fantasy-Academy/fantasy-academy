<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Export;

use FantasyAcademy\API\Entity\Challenge;
use FantasyAcademy\API\Entity\Question;
use FantasyAcademy\API\Repository\ChallengeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\Uid\Uuid;

readonly final class ChallengesExport
{
    public function __construct(
        private ChallengeRepository $challengeRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param array<string> $challengeIds
     */
    public function exportChallenges(array $challengeIds): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        // First sheet: Challenges
        $challengesSheet = $spreadsheet->getActiveSheet();
        $challengesSheet->setTitle('Challenges');
        $challengesSheet->fromArray([
            'id',
            'name',
            'short_description',
            'description',
            'image',
            'max_points',
            'starts_at',
            'expires_at',
            'hint_text',
            'hint_image',
            'show_statistics_continuously',
            'gameweek',
            'skill_analytical',
            'skill_strategicplanning',
            'skill_adaptability',
            'skill_premierleagueknowledge',
            'skill_riskmanagement',
            'skill_decisionmakingunderpressure',
            'skill_financialmanagement',
            'skill_longtermvision',
        ]);

        $challengesData = [];
        $questionsData = [];

        foreach ($challengeIds as $challengeId) {
            $challenge = $this->challengeRepository->get(Uuid::fromString($challengeId));

            $challengesData[] = [
                $challenge->id->toRfc4122(),
                $challenge->name,
                $challenge->shortDescription,
                $challenge->description,
                $challenge->image,
                $challenge->maxPoints,
                $challenge->startsAt->format('Y-m-d H:i:s'),
                $challenge->expiresAt->format('Y-m-d H:i:s'),
                $challenge->hintText,
                $challenge->hintImage,
                $challenge->showStatisticsContinuously ? 1 : 0,
                $challenge->gameweek,
                $challenge->skillAnalytical,
                $challenge->skillStrategicPlanning,
                $challenge->skillAdaptability,
                $challenge->skillPremierLeagueKnowledge,
                $challenge->skillRiskManagement,
                $challenge->skillDecisionMakingUnderPressure,
                $challenge->skillFinancialManagement,
                $challenge->skillLongTermVision,
            ];

            // Get all questions for this challenge
            $questions = $this->getQuestionsForChallenge($challenge);

            foreach ($questions as $question) {
                $questionsData[] = $this->formatQuestionRow($question);
            }
        }

        if (count($challengesData) > 0) {
            $challengesSheet->fromArray($challengesData, startCell: 'A2');
        }

        // Second sheet: Questions
        $questionsSheet = $spreadsheet->createSheet();
        $questionsSheet->setTitle('Questions');
        $questionsSheet->fromArray([
            'question_id',
            'challenge_id',
            'text',
            'type',
            'image',
            'numeric_type_min',
            'numeric_type_max',
            'choices',
            'choices_min_selections',
            'choices_max_selections',
            'correct_text_answer',
            'correct_numeric_answer',
            'correct_selected_choice_text',
            'correct_selected_choice_texts',
            'correct_ordered_choice_texts',
        ]);

        if (count($questionsData) > 0) {
            $questionsSheet->fromArray($questionsData, startCell: 'A2');
        }

        return $spreadsheet;
    }

    /**
     * @return array<Question>
     */
    private function getQuestionsForChallenge(Challenge $challenge): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select('q')
            ->from(Question::class, 'q')
            ->where('q.challenge = :challenge')
            ->setParameter('challenge', $challenge);

        /** @var array<Question> */
        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<mixed>
     */
    private function formatQuestionRow(Question $question): array
    {
        $choicesText = null;
        $minSelections = null;
        $maxSelections = null;

        if ($question->choiceConstraint !== null) {
            // Format choices as JSON array of objects for import compatibility
            $choicesArray = array_map(
                fn ($choice) => [
                    'text' => $choice->text,
                    'description' => $choice->description,
                    'image' => $choice->image,
                ],
                $question->choiceConstraint->choices
            );
            $choicesText = json_encode($choicesArray, JSON_THROW_ON_ERROR);
            $minSelections = $question->choiceConstraint->minSelections;
            $maxSelections = $question->choiceConstraint->maxSelections;
        }

        $numericMin = $question->numericConstraint?->min;
        $numericMax = $question->numericConstraint?->max;

        // Extract correct answer data
        $correctTextAnswer = null;
        $correctNumericAnswer = null;
        $correctSelectedChoiceText = null;
        $correctSelectedChoiceTexts = null;
        $correctOrderedChoiceTexts = null;

        if ($question->correctAnswer !== null) {
            $correctTextAnswer = $question->correctAnswer->textAnswer;
            $correctNumericAnswer = $question->correctAnswer->numericAnswer;

            // Convert choice IDs to text by looking them up in choiceConstraint
            if ($question->correctAnswer->selectedChoiceId !== null && $question->choiceConstraint !== null) {
                foreach ($question->choiceConstraint->choices as $choice) {
                    if ($choice->id->equals($question->correctAnswer->selectedChoiceId)) {
                        $correctSelectedChoiceText = $choice->text;
                        break;
                    }
                }
            }

            if ($question->correctAnswer->selectedChoiceIds !== null && count($question->correctAnswer->selectedChoiceIds) > 0 && $question->choiceConstraint !== null) {
                $selectedTexts = [];
                foreach ($question->correctAnswer->selectedChoiceIds as $selectedId) {
                    foreach ($question->choiceConstraint->choices as $choice) {
                        if ($choice->id->equals($selectedId)) {
                            $selectedTexts[] = $choice->text;
                            break;
                        }
                    }
                }
                if (count($selectedTexts) > 0) {
                    // Format as JSON array for import compatibility
                    $correctSelectedChoiceTexts = json_encode($selectedTexts, JSON_THROW_ON_ERROR);
                }
            }

            if ($question->correctAnswer->orderedChoiceIds !== null && count($question->correctAnswer->orderedChoiceIds) > 0 && $question->choiceConstraint !== null) {
                $orderedTexts = [];
                foreach ($question->correctAnswer->orderedChoiceIds as $orderedId) {
                    foreach ($question->choiceConstraint->choices as $choice) {
                        if ($choice->id->equals($orderedId)) {
                            $orderedTexts[] = $choice->text;
                            break;
                        }
                    }
                }
                if (count($orderedTexts) > 0) {
                    // Format as JSON array for import compatibility
                    $correctOrderedChoiceTexts = json_encode($orderedTexts, JSON_THROW_ON_ERROR);
                }
            }
        }

        return [
            $question->id->toRfc4122(),
            $question->challenge->id->toRfc4122(),
            $question->text,
            $question->type->value,
            $question->image,
            $numericMin,
            $numericMax,
            $choicesText,
            $minSelections,
            $maxSelections,
            $correctTextAnswer,
            $correctNumericAnswer,
            $correctSelectedChoiceText,
            $correctSelectedChoiceTexts,
            $correctOrderedChoiceTexts,
        ];
    }
}
