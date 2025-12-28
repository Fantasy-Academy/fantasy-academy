<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Import;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\Challenge;
use FantasyAcademy\API\Entity\PlayerAnsweredQuestion;
use FantasyAcademy\API\Entity\Question;
use FantasyAcademy\API\Exceptions\ImportFailed;
use FantasyAcademy\API\Services\ProvideIdentity;
use FantasyAcademy\API\Value\Answer;
use FantasyAcademy\API\Value\Choice;
use FantasyAcademy\API\Value\ChoiceQuestionConstraint;
use FantasyAcademy\API\Value\NumericQuestionConstraint;
use FantasyAcademy\API\Value\QuestionType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Psr\Clock\ClockInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type ImportChallengeRow array{
 *      id: string,
 *      name: string,
 *      short_description: string,
 *      description: string,
 *      image: null|string,
 *      max_points: string,
 *      starts_at: string,
 *      expires_at: string,
 *      hint_text: null|string,
 *      hint_image: null|string,
 *      show_statistics_continuously: null|string,
 *      gameweek: string,
 *      skill_analytical: string,
 *      skill_strategicplanning: string,
 *      skill_adaptability: string,
 *      skill_premierleagueknowledge: string,
 *      skill_riskmanagement: string,
 *      skill_decisionmakingunderpressure: string,
 *      skill_financialmanagement: string,
 *      skill_longtermvision: string,
 *  }
 *
 * @phpstan-type ImportQuestionRow array{
 *      question_id?: null|string,
 *      challenge_id: string,
 *      text: string,
 *      type: string,
 *      image: null|string,
 *      numeric_type_min: null|string,
 *      numeric_type_max: null|string,
 *      choices: null|string,
 *      choices_min_selections: null|string,
 *      choices_max_selections: null|string,
 *      correct_text_answer?: null|string,
 *      correct_numeric_answer?: null|string,
 *      correct_selected_choice_text?: null|string,
 *      correct_selected_choice_texts?: null|string,
 *      correct_ordered_choice_texts?: null|string,
 *  }
 */
readonly final class ChallengesImport
{
    public function __construct(
        private ProvideIdentity $provideIdentity,
        private EntityManagerInterface $entityManager,
        private ClockInterface $clock,
        private SpreadsheetReader $spreadsheetReader,
    ) {
    }

    public function importFile(UploadedFile $file): void
    {
        $spreadsheet = $this->loadSpreadsheet($file);

        $sheetChallenges = $spreadsheet->getSheet(0);
        $sheetQuestions = $spreadsheet->getSheet(1);

        /** @var array<int, array<string, string|null>> $challengesRows */
        $challengesRows = $this->spreadsheetReader->readSheetAsAssoc($sheetChallenges);

        /** @var array<int, array<string, string|null>> $questionsRows */
        $questionsRows = $this->spreadsheetReader->readSheetAsAssoc($sheetQuestions);

        $challengeIdKey = $this->getFirstColumnHeaderKey($sheetChallenges);

        $challengesById = $this->processChallengeRows($challengesRows, $challengeIdKey);
        $importedQuestionIds = $this->processQuestionRows($questionsRows, $challengesById);

        $this->deleteRemovedQuestions($challengesById, $importedQuestionIds);

        $this->entityManager->flush();
    }

    /**
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private function loadSpreadsheet(UploadedFile $file): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        $path = $file->getPathname();

        /** @var \PhpOffice\PhpSpreadsheet\Reader\Xlsx $reader */
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(false);

        /** @var array<int, string> $allSheets */
        $allSheets = $reader->listWorksheetNames($path);

        if (count($allSheets) < 2) {
            throw new ImportFailed('The workbook must contain at least two sheets.');
        }

        $challengeSheetName = $allSheets[0];
        $questionSheetName = $allSheets[1];
        $reader->setLoadSheetsOnly([$challengeSheetName, $questionSheetName]);

        return $reader->load($path);
    }

    /**
     * @param array<int, array<string, string|null>> $rows
     * @return array<string, Challenge>
     */
    private function processChallengeRows(array $rows, string $idKey): array
    {
        $challengesById = [];
        $excelRow = 2;

        foreach ($rows as $row) {
            $id = trim((string) ($row[$idKey] ?? ''));

            if ($id === '') {
                throw new ImportFailed('Missing challenge ID.', $excelRow, $idKey);
            }

            if (isset($challengesById[$id])) {
                throw new ImportFailed(sprintf('Duplicate challenge ID "%s".', $id), $excelRow, $idKey);
            }

            $this->assertChallengeRow($row, $excelRow);
            $challengesById[$id] = $this->createOrUpdateChallenge($row);
            $excelRow++;
        }

        return $challengesById;
    }

    /**
     * @param array<int, array<string, string|null>> $rows
     * @param array<string, Challenge> $challengesById
     * @return array<string, array<string>>
     */
    private function processQuestionRows(array $rows, array $challengesById): array
    {
        $importedQuestionIdsByChallenge = array_fill_keys(array_keys($challengesById), []);
        $excelRow = 2;

        foreach ($rows as $row) {
            $challengeId = trim((string) ($row['challenge_id'] ?? ''));

            if ($challengeId === '') {
                throw new ImportFailed('Empty challenge ID.', $excelRow, 'challenge_id');
            }

            if (!isset($challengesById[$challengeId])) {
                throw new ImportFailed(
                    sprintf('Non-existing challenge ID "%s".', $challengeId),
                    $excelRow,
                    'challenge_id'
                );
            }

            $this->assertQuestionRow($row, $excelRow);
            $question = $this->createOrUpdateQuestionById($row, $challengesById[$challengeId], $excelRow);
            $importedQuestionIdsByChallenge[$challengeId][] = $question->id->toRfc4122();
            $excelRow++;
        }

        return $importedQuestionIdsByChallenge;
    }

    private function getFirstColumnHeaderKey(Worksheet $ws): string
    {
        /** @var null|string $raw */
        $raw = $ws->getCell('A1')->getValue();

        return $this->spreadsheetReader->normalizeHeader((string) $raw);
    }

    /**
     * @param ImportChallengeRow $row
     */
    private function createOrUpdateChallenge(array $row): Challenge
    {
        // Try to find existing challenge by UUID from the ID column
        try {
            $uuid = Uuid::fromString($row['id']);
            $existingChallenge = $this->entityManager->find(Challenge::class, $uuid);

            if ($existingChallenge instanceof Challenge) {
                // Update existing challenge in place
                $existingChallenge->update(
                    name: $row['name'],
                    shortDescription: $row['short_description'],
                    description: $row['description'],
                    image: $row['image'],
                    startsAt: new DateTimeImmutable($row['starts_at']),
                    expiresAt: new DateTimeImmutable($row['expires_at']),
                    maxPoints: (int) $row['max_points'],
                    hintText: $row['hint_text'],
                    hintImage: $row['hint_image'],
                    skillAnalytical: $this->makePercentage($row['skill_analytical']),
                    skillStrategicPlanning: $this->makePercentage($row['skill_strategicplanning']),
                    skillAdaptability: $this->makePercentage($row['skill_adaptability']),
                    skillPremierLeagueKnowledge: $this->makePercentage($row['skill_premierleagueknowledge']),
                    skillRiskManagement: $this->makePercentage($row['skill_riskmanagement']),
                    skillDecisionMakingUnderPressure: $this->makePercentage($row['skill_decisionmakingunderpressure']),
                    skillFinancialManagement: $this->makePercentage($row['skill_financialmanagement']),
                    skillLongTermVision: $this->makePercentage($row['skill_longtermvision']),
                    showStatisticsContinuously: $this->makeBoolean($row['show_statistics_continuously'] ?? null),
                    gameweek: (int) $row['gameweek'],
                );

                return $existingChallenge;
            }
        } catch (\InvalidArgumentException $e) {
            // Not a valid UUID, will create new with generated UUID
        }

        return $this->createChallenge($row);
    }

    /**
     * @param ImportChallengeRow $row
     */
    private function createChallenge(array $row): Challenge
    {
        $challenge = new Challenge(
            id: $this->provideIdentity->next(),
            name: $row['name'],
            shortDescription: $row['short_description'],
            description: $row['description'],
            image: $row['image'],
            addedAt: $this->clock->now(),
            startsAt: new DateTimeImmutable($row['starts_at']),
            expiresAt: new DateTimeImmutable($row['expires_at']),
            maxPoints: (int) $row['max_points'],
            hintText: $row['hint_text'],
            hintImage: $row['hint_image'],
            skillAnalytical: $this->makePercentage($row['skill_analytical']),
            skillStrategicPlanning: $this->makePercentage($row['skill_strategicplanning']),
            skillAdaptability: $this->makePercentage($row['skill_adaptability']),
            skillPremierLeagueKnowledge: $this->makePercentage($row['skill_premierleagueknowledge']),
            skillRiskManagement: $this->makePercentage($row['skill_riskmanagement']),
            skillDecisionMakingUnderPressure: $this->makePercentage($row['skill_decisionmakingunderpressure']),
            skillFinancialManagement: $this->makePercentage($row['skill_financialmanagement']),
            skillLongTermVision: $this->makePercentage($row['skill_longtermvision']),
            showStatisticsContinuously: $this->makeBoolean($row['show_statistics_continuously'] ?? null),
            gameweek: (int) $row['gameweek'],
        );

        $this->entityManager->persist($challenge);

        return $challenge;
    }

    private function makePercentage(null|int|float|string $number): float
    {
        $number = (float) $number;

        if ($number < 1) {
            return $number;
        }

        return $number / 100;
    }

    private function makeBoolean(null|string $value): bool
    {
        if ($value === null || $value === '') {
            return true; // Default to true
        }

        $normalized = strtolower(trim($value));
        return in_array($normalized, ['true', '1', 'yes', 'y'], true);
    }

    /**
     * @param ImportQuestionRow $row
     */
    private function createNumericConstraint(array $row): ?NumericQuestionConstraint
    {
        if ($row['numeric_type_min'] === null && $row['numeric_type_max'] === null) {
            return null;
        }

        return new NumericQuestionConstraint(
            min: $row['numeric_type_min'] !== null ? (int) $row['numeric_type_min'] : null,
            max: $row['numeric_type_max'] !== null ? (int) $row['numeric_type_max'] : null,
        );
    }

    /**
     * Create or update a question based on the question_id column.
     * - If question_id is empty: create a new question
     * - If question_id is a valid UUID: update the existing question
     *
     * @param ImportQuestionRow $row
     */
    private function createOrUpdateQuestionById(array $row, Challenge $challenge, int $excelRow): Question
    {
        $questionId = isset($row['question_id']) ? trim((string) $row['question_id']) : '';

        // If question_id is empty, create a new question
        if ($questionId === '') {
            return $this->createQuestion($row, $challenge);
        }

        // Try to find existing question by UUID
        try {
            $uuid = Uuid::fromString($questionId);
            $existingQuestion = $this->entityManager->find(Question::class, $uuid);

            if (!$existingQuestion instanceof Question) {
                throw new ImportFailed(
                    sprintf('Question with ID "%s" not found.', $questionId),
                    $excelRow,
                    'question_id'
                );
            }

            // Verify the question belongs to the correct challenge
            if (!$existingQuestion->challenge->id->equals($challenge->id)) {
                throw new ImportFailed(
                    sprintf('Question with ID "%s" belongs to a different challenge.', $questionId),
                    $excelRow,
                    'question_id'
                );
            }

            // Update the existing question with all properties
            return $this->updateQuestion($existingQuestion, $row);
        } catch (\InvalidArgumentException) {
            throw new ImportFailed(
                sprintf('Invalid question ID format "%s".', $questionId),
                $excelRow,
                'question_id'
            );
        }
    }

    /**
     * Update an existing question with all properties from the import row.
     *
     * @param ImportQuestionRow $row
     */
    private function updateQuestion(Question $question, array $row): Question
    {
        $numericConstraint = $this->createNumericConstraint($row);
        $choiceConstraint = $this->createChoiceConstraintFromRow($row, $question->choiceConstraint);
        $correctAnswer = $this->createCorrectAnswerFromRow($row, $choiceConstraint);

        $question->update(
            text: $row['text'],
            type: QuestionType::from($row['type']),
            image: $row['image'],
            numericConstraint: $numericConstraint,
            choiceConstraint: $choiceConstraint,
            correctAnswer: $correctAnswer,
        );

        return $question;
    }

    /**
     * Delete questions that exist in the database but are not in the import.
     * Throws ImportFailed if any question to be deleted has player answers.
     *
     * @param array<string, Challenge> $challengesById
     * @param array<string, array<string>> $importedQuestionIdsByChallenge
     */
    private function deleteRemovedQuestions(array $challengesById, array $importedQuestionIdsByChallenge): void
    {
        if (count($challengesById) === 0) {
            return;
        }

        // Get all existing questions for all imported challenges in ONE query
        $challengeUuids = array_map(fn (Challenge $c) => $c->id, array_values($challengesById));
        $existingQuestions = $this->getExistingQuestionsForChallenges($challengeUuids);

        // Find questions to delete (not in import)
        $questionsToDelete = [];
        foreach ($existingQuestions as $question) {
            $challengeId = $question->challenge->id->toRfc4122();
            $importedIds = $importedQuestionIdsByChallenge[$challengeId] ?? [];

            if (!in_array($question->id->toRfc4122(), $importedIds, true)) {
                $questionsToDelete[] = $question;
            }
        }

        if (count($questionsToDelete) === 0) {
            return;
        }

        // Check which questions have answers in ONE query
        $questionUuids = array_map(fn (Question $q) => $q->id, $questionsToDelete);
        $questionIdsWithAnswers = $this->getQuestionIdsWithAnswers($questionUuids);

        foreach ($questionsToDelete as $question) {
            if (in_array($question->id->toRfc4122(), $questionIdsWithAnswers, true)) {
                throw new ImportFailed(
                    sprintf('Cannot delete question "%s" because it has player answers.', $question->text)
                );
            }
            $this->entityManager->remove($question);
        }
    }

    /**
     * Get all existing questions for multiple challenges in ONE query.
     *
     * @param array<Uuid> $challengeIds
     * @return array<Question>
     */
    private function getExistingQuestionsForChallenges(array $challengeIds): array
    {
        if (count($challengeIds) === 0) {
            return [];
        }

        /** @var array<Question> */
        return $this->entityManager->createQueryBuilder()
            ->select('q')
            ->from(Question::class, 'q')
            ->where('q.challenge IN (:challenges)')
            ->setParameter('challenges', $challengeIds)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get IDs of questions that have player answers in ONE query.
     *
     * @param array<Uuid> $questionIds
     * @return array<string> UUIDs as strings
     */
    private function getQuestionIdsWithAnswers(array $questionIds): array
    {
        if (count($questionIds) === 0) {
            return [];
        }

        /** @var array<array{question_id: string}> $results */
        $results = $this->entityManager->createQueryBuilder()
            ->select('DISTINCT IDENTITY(paq.question) as question_id')
            ->from(PlayerAnsweredQuestion::class, 'paq')
            ->where('paq.question IN (:questions)')
            ->setParameter('questions', $questionIds)
            ->getQuery()
            ->getArrayResult();

        return array_column($results, 'question_id');
    }

    /**
     * @param ImportQuestionRow $row
     */
    private function createQuestion(array $row, Challenge $challenge): Question
    {
        $numericConstraint = $this->createNumericConstraint($row);
        $choiceConstraint = $this->createChoiceConstraintFromRow($row);
        $correctAnswer = $this->createCorrectAnswerFromRow($row, $choiceConstraint);

        $question = new Question(
            id: $this->provideIdentity->next(),
            challenge: $challenge,
            text: $row['text'],
            type: QuestionType::from($row['type']),
            image: $row['image'],
            numericConstraint: $numericConstraint,
            choiceConstraint: $choiceConstraint,
            correctAnswer: $correctAnswer,
        );

        $this->entityManager->persist($question);

        return $question;
    }

    /**
     * @param ImportQuestionRow $row
     */
    private function createChoiceConstraintFromRow(
        array $row,
        ?ChoiceQuestionConstraint $existingConstraint = null
    ): ?ChoiceQuestionConstraint {
        if ($row['choices'] === null || !json_validate($row['choices'])) {
            return null;
        }

        /**
         * @var array<array{
         *     text: string,
         *     description: null|string,
         *     image?: null|string,
         * }> $choicesInfo
         */
        $choicesInfo = json_decode($row['choices'], associative: true);

        // Build lookup map for existing choices (for ID reuse when updating)
        $existingChoicesByText = [];
        if ($existingConstraint !== null) {
            foreach ($existingConstraint->choices as $choice) {
                $existingChoicesByText[$choice->text] = $choice;
            }
        }

        $choices = [];
        foreach ($choicesInfo as $choiceData) {
            $choiceId = isset($existingChoicesByText[$choiceData['text']])
                ? $existingChoicesByText[$choiceData['text']]->id
                : $this->provideIdentity->next();

            $choices[] = new Choice(
                id: $choiceId,
                text: $choiceData['text'],
                description: $choiceData['description'],
                image: $choiceData['image'] ?? null,
            );
        }

        return new ChoiceQuestionConstraint(
            choices: $choices,
            minSelections: $row['choices_min_selections'] !== null ? (int) $row['choices_min_selections'] : null,
            maxSelections: $row['choices_max_selections'] !== null ? (int) $row['choices_max_selections'] : null,
        );
    }

    /**
     * @param ImportQuestionRow $row
     */
    private function createCorrectAnswerFromRow(array $row, null|ChoiceQuestionConstraint $choiceConstraint): null|Answer
    {
        $textAnswer = $row['correct_text_answer'] ?? null;
        $numericAnswer = $row['correct_numeric_answer'] ?? null;
        $selectedChoiceText = $row['correct_selected_choice_text'] ?? null;
        $selectedChoiceTexts = $row['correct_selected_choice_texts'] ?? null;
        $orderedChoiceTexts = $row['correct_ordered_choice_texts'] ?? null;

        // Check if any correct answer field is provided
        $hasAnyCorrectAnswer = $textAnswer !== null && trim($textAnswer) !== '' ||
            $numericAnswer !== null && trim($numericAnswer) !== '' ||
            $selectedChoiceText !== null && trim($selectedChoiceText) !== '' ||
            $selectedChoiceTexts !== null && trim($selectedChoiceTexts) !== '' ||
            $orderedChoiceTexts !== null && trim($orderedChoiceTexts) !== '';

        if (!$hasAnyCorrectAnswer) {
            return null;
        }

        // Map choice texts to choice IDs if needed
        $selectedChoiceId = null;
        $selectedChoiceIds = null;
        $orderedChoiceIds = null;

        if ($selectedChoiceText !== null && $choiceConstraint !== null) {
            foreach ($choiceConstraint->choices as $choice) {
                if ($choice->text === trim($selectedChoiceText)) {
                    $selectedChoiceId = $choice->id;
                    break;
                }
            }
        }

        if ($selectedChoiceTexts !== null && json_validate($selectedChoiceTexts) && $choiceConstraint !== null) {
            /** @var array<string> $texts */
            $texts = json_decode($selectedChoiceTexts, associative: true);
            $selectedChoiceIds = [];

            foreach ($texts as $text) {
                foreach ($choiceConstraint->choices as $choice) {
                    if ($choice->text === trim($text)) {
                        $selectedChoiceIds[] = $choice->id;
                        break;
                    }
                }
            }
        }

        if ($orderedChoiceTexts !== null && json_validate($orderedChoiceTexts) && $choiceConstraint !== null) {
            /** @var array<string> $texts */
            $texts = json_decode($orderedChoiceTexts, associative: true);
            $orderedChoiceIds = [];

            foreach ($texts as $text) {
                foreach ($choiceConstraint->choices as $choice) {
                    if ($choice->text === trim($text)) {
                        $orderedChoiceIds[] = $choice->id;
                        break;
                    }
                }
            }
        }

        return new Answer(
            textAnswer: $textAnswer !== null && trim($textAnswer) !== '' ? trim($textAnswer) : null,
            numericAnswer: $numericAnswer !== null && trim($numericAnswer) !== '' ? (float) $numericAnswer : null,
            selectedChoiceId: $selectedChoiceId,
            selectedChoiceIds: $selectedChoiceIds,
            orderedChoiceIds: $orderedChoiceIds,
        );
    }

    /**
     * @phpstan-assert ImportChallengeRow $row
     * @phpstan-ignore-next-line
     */
    private function assertChallengeRow(array $row, int $excelRow): void
    {
        // Minimal runtime presence checks for required keys; extend as needed
        foreach ([
            'id','name','short_description','description','max_points','starts_at','expires_at',
            'gameweek',
            'skill_analytical','skill_strategicplanning','skill_adaptability','skill_premierleagueknowledge',
            'skill_riskmanagement','skill_decisionmakingunderpressure','skill_financialmanagement',
            'skill_longtermvision',
        ] as $key) {
            if (!array_key_exists($key, $row)) {
                throw new ImportFailed('Missing required column.', $excelRow, $key);
            }
        }
    }

    /**
     * @phpstan-assert ImportQuestionRow $row
     * @phpstan-ignore-next-line
     */
    private function assertQuestionRow(array $row, int $excelRow): void
    {
        foreach ([
            'challenge_id','text','type','image','numeric_type_min','numeric_type_max','choices','choices_min_selections','choices_max_selections',
        ] as $key) {
            if (!array_key_exists($key, $row)) {
                throw new ImportFailed('Missing required column.', $excelRow, $key);
            }
        }

        // Validate choices format if provided
        if ($row['choices'] !== null && is_string($row['choices']) && trim($row['choices']) !== '') {
            $choicesJson = $row['choices'];
            if (!json_validate($choicesJson)) {
                throw new ImportFailed('Invalid JSON format.', $excelRow, 'choices');
            }

            $choicesData = json_decode($choicesJson, associative: true);

            if (!is_array($choicesData) || !array_is_list($choicesData)) {
                throw new ImportFailed('Must be a JSON array.', $excelRow, 'choices');
            }

            foreach ($choicesData as $index => $choice) {
                if (!is_array($choice)) {
                    throw new ImportFailed(sprintf('Choice at index %d must be an object with "text" and "description" fields.', $index), $excelRow, 'choices');
                }

                if (!array_key_exists('text', $choice)) {
                    throw new ImportFailed(sprintf('Choice at index %d is missing required field "text".', $index), $excelRow, 'choices');
                }

                if (!array_key_exists('description', $choice)) {
                    throw new ImportFailed(sprintf('Choice at index %d is missing required field "description".', $index), $excelRow, 'choices');
                }
            }
        }
    }
}
