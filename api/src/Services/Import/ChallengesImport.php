<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Import;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\Challenge;
use FantasyAcademy\API\Entity\Question;
use FantasyAcademy\API\Exceptions\ImportFailed;
use FantasyAcademy\API\Services\ProvideIdentity;
use FantasyAcademy\API\Value\Choice;
use FantasyAcademy\API\Value\ChoiceQuestionConstraint;
use FantasyAcademy\API\Value\NumericQuestionConstraint;
use FantasyAcademy\API\Value\QuestionType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Psr\Clock\ClockInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
 *      challenge_id: string,
 *      text: string,
 *      type: string,
 *      image: null|string,
 *      numeric_type_min: null|string,
 *      numeric_type_max: null|string,
 *      choices: null|string,
 *      choices_min_selections: null|string,
 *      choices_max_selections: null|string,
 *  }
 */
readonly final class ChallengesImport
{
    public function __construct(
        private ProvideIdentity $provideIdentity,
        private EntityManagerInterface $entityManager,
        private ClockInterface $clock,
    ) {
    }

    public function importFile(UploadedFile $file): void
    {
        $path = $file->getPathname();

        /** @var \PhpOffice\PhpSpreadsheet\Reader\Xlsx $reader */
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(false); // load styles/number formats so we can get formatted text

        /** @var array<int, string> $allSheets */
        $allSheets = $reader->listWorksheetNames($path);

        if (count($allSheets) < 2) {
            throw new ImportFailed('The workbook must contain at least two sheets.');
        }

        // First sheet: challenges; Second sheet: questions
        $challengeSheetName = $allSheets[0];
        $questionSheetName  = $allSheets[1];
        $reader->setLoadSheetsOnly([$challengeSheetName, $questionSheetName]);

        $spreadsheet = $reader->load($path);

        $sheetChallenges = $spreadsheet->getSheetByName($challengeSheetName) ?? $spreadsheet->getSheet(0);
        $sheetQuestions  = $spreadsheet->getSheetByName($questionSheetName) ?? $spreadsheet->getSheet(1);

        /** @var array<int, array<string, string|null>> $challengesRows */
        $challengesRows = $this->readSheetAsAssoc($sheetChallenges);

        /** @var array<int, array<string, string|null>> $questionsRows */
        $questionsRows = $this->readSheetAsAssoc($sheetQuestions);

        // Determine the header key (normalized) for the first column on each sheet
        $challengeIdKey = $this->getFirstColumnHeaderKey($sheetChallenges);
        $challengeIdOfQuestionKey = $this->getFirstColumnHeaderKey($sheetQuestions);

        /** @var array<string, Challenge> $challengesById */
        $challengesById = [];

        foreach ($challengesRows as $row) {
            $id = (string) ($row[$challengeIdKey] ?? '');
            $id = trim($id);

            if ($id === '') {
                throw new ImportFailed('Missing challenge ID in the challenges sheet');
            }

            if (isset($challengesById[$id])) {
                throw new ImportFailed(sprintf('Duplicate challenge ID "%s" found in the challenges sheet.', $id));
            }

            $this->assertChallengeRow($row);
            $challengesById[$id] = $this->createChallenge($row);
        }

        // Validate questions: first column must reference an existing challenge ID. Throw if not found or if empty.
        foreach ($questionsRows as $row) {
            $challengeId = isset($row[$challengeIdOfQuestionKey]) ? (string) $row[$challengeIdOfQuestionKey] : '';
            $challengeId = trim($challengeId);

            if ($challengeId === '') {
                throw new ImportFailed(sprintf('Question has empty challenge ID in the first column on sheet "%s".', $questionSheetName));
            }

            if (!isset($challengesById[$challengeId])) {
                throw new ImportFailed(sprintf('Question references non-existing challenge ID "%s".', $challengeId));
            }

            $this->assertQuestionRow($row);
            $this->createQuestion($row, $challengesById[$challengeId]);
        }

        $this->entityManager->flush();
    }

    private function getFirstColumnHeaderKey(Worksheet $ws): string
    {
        /** @var string $raw */
        $raw = $ws->getCell('A1')->getValue();

        return $this->normalizeHeader($raw);
    }

    /**
     * Reads a worksheet into an array of associative rows using the first row as headers.
     * Values are taken as displayed text (strings), preserving nulls for empty cells.
     * Empty rows are skipped.
     *
     * @return list<array<mixed>>
     */
    private function readSheetAsAssoc(Worksheet $ws): array
    {
        $highestRow = $ws->getHighestRow();
        $highestColumn = $ws->getHighestColumn();

        // Build header map from row 1
        $headerRow = $ws->rangeToArray("A1:{$highestColumn}1", null, true, true, true)[1] ?? [];
        $headers = [];

        foreach ($headerRow as $col => $value) {
            /** @var null|string $value */
            $headers[$col] = $this->normalizeHeader((string) $value);
        }

        $rows = [];

        for ($r = 2; $r <= $highestRow; $r++) {
            $rowCells = $ws->rangeToArray("A{$r}:{$highestColumn}{$r}", null, true, true, true)[$r] ?? [];
            $assoc = [];
            $allEmpty = true;

            foreach ($rowCells as $col => $value) {
                $key = $headers[$col] ?? $col;

                // With formatData=true (rangeToArray) and readDataOnly=false, $value is the
                // displayed text from Excel. Keep it as-is (string) and preserve nulls.
                if ($value !== null && $value !== '') {
                    $allEmpty = false;
                }

                $assoc[$key] = $value === null ? null : $value;
            }

            if (!$allEmpty) {
                $rows[] = $assoc;
            }
        }

        return $rows;
    }

    /**
     * Normalizes header names to snake_case alphanumeric keys.
     */
    private function normalizeHeader(string $raw): string
    {
        $k = strtolower(trim($raw));
        $k = preg_replace('/\s+/', '_', $k) ?? '';
        $k = preg_replace('/[^a-z0-9_]/', '', $k) ?? '';

        return $k !== '' ? $k : 'col';
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
    private function createQuestion(array $row, Challenge $challenge): Question
    {
        $numericConstraint = null;
        $choiceConstraint = null;

        if ($row['numeric_type_min'] !== null || $row['numeric_type_max'] !== null) {
            $numericConstraint = new NumericQuestionConstraint(
                min: $row['numeric_type_min'] !== null ? (int) $row['numeric_type_min'] : null,
                max: $row['numeric_type_max'] !== null ? (int) $row['numeric_type_max'] : null,
            );
        }

        if ($row['choices'] !== null && json_validate($row['choices'])) {
            /**
             * @var array<array{
             *     text: string,
             *     description: null|string,
             *     image?: null|string,
             * }> $choicesInfo
             */
            $choicesInfo = json_decode($row['choices'], associative: true);
            $choices = [];

            foreach ($choicesInfo as $choice) {
                $choices[] = new Choice(
                    id: $this->provideIdentity->next(),
                    text: $choice['text'],
                    description: $choice['description'],
                    image: $choice['image'] ?? null,
                );
            }

            $choiceConstraint = new ChoiceQuestionConstraint(
                choices: $choices,
                minSelections: $row['choices_min_selections'] !== null ? (int) $row['choices_min_selections'] : null,
                maxSelections: $row['choices_max_selections'] !== null ? (int) $row['choices_max_selections'] : null,
            );
        }

        $question = new Question(
            id: $this->provideIdentity->next(),
            challenge: $challenge,
            text: $row['text'],
            type: QuestionType::from($row['type']),
            image: $row['image'],
            numericConstraint: $numericConstraint,
            choiceConstraint: $choiceConstraint,
        );

        $this->entityManager->persist($question);

        return $question;
    }

    /**
     * @phpstan-assert ImportChallengeRow $row
     * @phpstan-ignore-next-line
     */
    private function assertChallengeRow(array $row): void
    {
        // Minimal runtime presence checks for required keys; extend as needed
        foreach ([
            'id','name','short_description','description','max_points','starts_at','expires_at',
            'skill_analytical','skill_strategicplanning','skill_adaptability','skill_premierleagueknowledge',
            'skill_riskmanagement','skill_decisionmakingunderpressure','skill_financialmanagement',
            'skill_longtermvision',
        ] as $key) {
            if (!array_key_exists($key, $row)) {
                throw new ImportFailed(sprintf('Missing required challenge column "%s".', $key));
            }
        }
    }

    /**
     * @phpstan-assert ImportQuestionRow $row
     * @phpstan-ignore-next-line
     */
    private function assertQuestionRow(array $row): void
    {
        foreach ([
            'challenge_id','text','type','image','numeric_type_min','numeric_type_max','choices','choices_min_selections','choices_max_selections',
        ] as $key) {
            if (!array_key_exists($key, $row)) {
                throw new ImportFailed(sprintf('Missing required question column "%s".', $key));
            }
        }
    }
}
