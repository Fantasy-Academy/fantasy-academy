<?php

declare(strict_types=1);

/**
 * Script to generate Excel test files for ChallengesImport tests.
 * Run from project root: docker compose exec api php tests/imports/challenge/create_test_files.php
 */

require __DIR__ . '/../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Helper function to create a spreadsheet with standard headers
function createBaseSpreadsheet(): Spreadsheet
{
    $spreadsheet = new Spreadsheet();

    // Challenges sheet (first sheet)
    $challengesSheet = $spreadsheet->getActiveSheet();
    $challengesSheet->setTitle('Challenges');
    $challengesSheet->fromArray([
        'ID',
        'Name',
        'Short Description',
        'Description',
        'Image',
        'Max Points',
        'Starts At',
        'Expires At',
        'Hint Text',
        'Hint Image',
        'Skill Analytical',
        'Skill StrategicPlanning',
        'Skill Adaptability',
        'Skill PremierLeagueKnowledge',
        'Skill RiskManagement',
        'Skill DecisionMakingUnderPressure',
        'Skill FinancialManagement',
        'Skill LongTermVision',
    ], null, 'A1');

    // Questions sheet (second sheet)
    $questionsSheet = $spreadsheet->createSheet();
    $questionsSheet->setTitle('Questions');
    $questionsSheet->fromArray([
        'Challenge ID',
        'Text',
        'Type',
        'Image',
        'Numeric Type Min',
        'Numeric Type Max',
        'Choices',
        'Choices Min Selections',
        'Choices Max Selections',
    ], null, 'A1');

    return $spreadsheet;
}

// Helper function to create a spreadsheet with correct answer columns
function createBaseSpreadsheetWithCorrectAnswers(): Spreadsheet
{
    $spreadsheet = new Spreadsheet();

    // Challenges sheet (first sheet)
    $challengesSheet = $spreadsheet->getActiveSheet();
    $challengesSheet->setTitle('Challenges');
    $challengesSheet->fromArray([
        'ID',
        'Name',
        'Short Description',
        'Description',
        'Image',
        'Max Points',
        'Starts At',
        'Expires At',
        'Hint Text',
        'Hint Image',
        'Skill Analytical',
        'Skill StrategicPlanning',
        'Skill Adaptability',
        'Skill PremierLeagueKnowledge',
        'Skill RiskManagement',
        'Skill DecisionMakingUnderPressure',
        'Skill FinancialManagement',
        'Skill LongTermVision',
    ], null, 'A1');

    // Questions sheet (second sheet)
    $questionsSheet = $spreadsheet->createSheet();
    $questionsSheet->setTitle('Questions');
    $questionsSheet->fromArray([
        'Challenge ID',
        'Text',
        'Type',
        'Image',
        'Numeric Type Min',
        'Numeric Type Max',
        'Choices',
        'Choices Min Selections',
        'Choices Max Selections',
        'Correct Text Answer',
        'Correct Numeric Answer',
        'Correct Selected Choice Text',
        'Correct Selected Choice Texts',
        'Correct Ordered Choice Texts',
    ], null, 'A1');

    return $spreadsheet;
}

// Valid import file with 2 challenges and various question types
function createValidFile(): void
{
    $spreadsheet = createBaseSpreadsheet();

    $challengesSheet = $spreadsheet->getSheet(0);
    $challengesSheet->fromArray([
        'C001',
        'The Transfer Window Challenge',
        'Make smart transfers',
        'You have £100m to build your dream team. Choose wisely and maximize your points.',
        'https://example.com/transfer.jpg',
        '1000',
        '2024-01-01 00:00:00',
        '2024-12-31 23:59:59',
        'Consider player form and fixtures',
        'https://example.com/hint.jpg',
        '25',
        '30',
        '15',
        '40',
        '20',
        '25',
        '35',
        '10',
    ], null, 'A2');

    $challengesSheet->fromArray([
        'C002',
        'Captain Choice Masterclass',
        'Pick the best captain',
        'Your captain scores double points. Who will you choose this gameweek?',
        null,
        '500',
        '2024-06-01 00:00:00',
        '2024-06-30 23:59:59',
        null,
        null,
        '0.3',
        '0.2',
        '0.25',
        '0.5',
        '0.4',
        '0.35',
        '0.1',
        '0.15',
    ], null, 'A3');

    $questionsSheet = $spreadsheet->getSheet(1);
    $questionsSheet->fromArray([
        'C001',
        'Who will you transfer in as your premium midfielder?',
        'single_select',
        null,
        null,
        null,
        json_encode([
            ['text' => 'Mohamed Salah', 'description' => 'Liverpool star with great form', 'image' => 'https://example.com/salah.jpg'],
            ['text' => 'Kevin De Bruyne', 'description' => 'Man City playmaker', 'image' => null],
            ['text' => 'Bruno Fernandes', 'description' => 'Manchester United captain'],
        ]),
        '1',
        '1',
    ], null, 'A2');

    $questionsSheet->fromArray([
        'C001',
        'Select your defensive picks (choose 2)',
        'multi_select',
        null,
        null,
        null,
        json_encode([
            ['text' => 'William Saliba', 'description' => 'Arsenal defender'],
            ['text' => 'Virgil van Dijk', 'description' => 'Liverpool defender'],
            ['text' => 'Ruben Dias', 'description' => 'Man City defender'],
            ['text' => 'Cristian Romero', 'description' => 'Spurs defender'],
        ]),
        '2',
        '2',
    ], null, 'A3');

    $questionsSheet->fromArray([
        'C001',
        'How many goals will your team score this gameweek?',
        'numeric',
        null,
        '0',
        '50',
        null,
        null,
        null,
    ], null, 'A4');

    $questionsSheet->fromArray([
        'C001',
        'Explain your transfer strategy in one sentence',
        'text',
        'https://example.com/strategy.jpg',
        null,
        null,
        null,
        null,
        null,
    ], null, 'A5');

    $questionsSheet->fromArray([
        'C002',
        'Who is your captain for this gameweek?',
        'single_select',
        null,
        null,
        null,
        json_encode([
            ['text' => 'Erling Haaland', 'description' => 'Man City striker'],
            ['text' => 'Harry Kane', 'description' => 'Bayern Munich forward'],
        ]),
        null,
        null,
    ], null, 'A6');

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/challenge_import_valid.xlsx');
}

// Missing challenge column (Name column removed)
function createMissingChallengeColumnFile(): void
{
    $spreadsheet = createBaseSpreadsheet();
    $challengesSheet = $spreadsheet->getSheet(0);
    // Remove the "Name" column (column B)
    $challengesSheet->removeColumn('B');
    // Add a data row to trigger validation
    $challengesSheet->fromArray([
        'C001',
        // 'Name' column missing
        'Short desc',
        'Full description',
        null,
        '100',
        '2024-01-01 00:00:00',
        '2024-12-31 23:59:59',
        null,
        null,
        '10', '10', '10', '10', '10', '10', '10', '10',
    ], null, 'A2');

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/challenge_import_missing_challenge_column.xlsx');
}

// Missing question column (Type column removed)
function createMissingQuestionColumnFile(): void
{
    $spreadsheet = createBaseSpreadsheet();
    $challengesSheet = $spreadsheet->getSheet(0);
    $challengesSheet->fromArray([
        'C001',
        'Test Challenge',
        'Short desc',
        'Full description',
        null,
        '100',
        '2024-01-01 00:00:00',
        '2024-12-31 23:59:59',
        null,
        null,
        '10', '10', '10', '10', '10', '10', '10', '10',
    ], null, 'A2');

    $questionsSheet = $spreadsheet->getSheet(1);
    // Remove the "Type" column (column C)
    $questionsSheet->removeColumn('C');
    // Add a data row to trigger validation
    $questionsSheet->fromArray([
        'C001',
        'Test question',
        // 'Type' column missing
        null,
        null, null, null, null, null,
    ], null, 'A2');

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/challenge_import_missing_question_column.xlsx');
}

// Duplicate challenge ID
function createDuplicateChallengeIdFile(): void
{
    $spreadsheet = createBaseSpreadsheet();
    $challengesSheet = $spreadsheet->getSheet(0);
    $challengesSheet->fromArray([
        'C001',
        'First Challenge',
        'Short desc',
        'Full description',
        null,
        '100',
        '2024-01-01 00:00:00',
        '2024-12-31 23:59:59',
        null,
        null,
        '10', '10', '10', '10', '10', '10', '10', '10',
    ], null, 'A2');

    $challengesSheet->fromArray([
        'C001', // Duplicate!
        'Second Challenge',
        'Short desc 2',
        'Full description 2',
        null,
        '200',
        '2024-01-01 00:00:00',
        '2024-12-31 23:59:59',
        null,
        null,
        '10', '10', '10', '10', '10', '10', '10', '10',
    ], null, 'A3');

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/challenge_import_duplicate_challenge_id.xlsx');
}

// Invalid challenge reference (question references non-existent challenge)
function createInvalidChallengeReferenceFile(): void
{
    $spreadsheet = createBaseSpreadsheet();
    $challengesSheet = $spreadsheet->getSheet(0);
    $challengesSheet->fromArray([
        'C001',
        'Test Challenge',
        'Short desc',
        'Full description',
        null,
        '100',
        '2024-01-01 00:00:00',
        '2024-12-31 23:59:59',
        null,
        null,
        '10', '10', '10', '10', '10', '10', '10', '10',
    ], null, 'A2');

    $questionsSheet = $spreadsheet->getSheet(1);
    $questionsSheet->fromArray([
        'C999', // Non-existent challenge ID!
        'Invalid question',
        'text',
        null,
        null, null, null, null, null,
    ], null, 'A2');

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/challenge_import_invalid_challenge_reference.xlsx');
}

// Empty challenge ID
function createEmptyChallengeIdFile(): void
{
    $spreadsheet = createBaseSpreadsheet();
    $challengesSheet = $spreadsheet->getSheet(0);
    $challengesSheet->fromArray([
        '', // Empty ID!
        'Test Challenge',
        'Short desc',
        'Full description',
        null,
        '100',
        '2024-01-01 00:00:00',
        '2024-12-31 23:59:59',
        null,
        null,
        '10', '10', '10', '10', '10', '10', '10', '10',
    ], null, 'A2');

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/challenge_import_empty_challenge_id.xlsx');
}

// Empty question challenge ID
function createEmptyQuestionChallengeIdFile(): void
{
    $spreadsheet = createBaseSpreadsheet();
    $challengesSheet = $spreadsheet->getSheet(0);
    $challengesSheet->fromArray([
        'C001',
        'Test Challenge',
        'Short desc',
        'Full description',
        null,
        '100',
        '2024-01-01 00:00:00',
        '2024-12-31 23:59:59',
        null,
        null,
        '10', '10', '10', '10', '10', '10', '10', '10',
    ], null, 'A2');

    $questionsSheet = $spreadsheet->getSheet(1);
    $questionsSheet->fromArray([
        '', // Empty challenge ID!
        'Test question',
        'text',
        null,
        null, null, null, null, null,
    ], null, 'A2');

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/challenge_import_empty_question_challenge_id.xlsx');
}

// Single sheet only (missing Questions sheet)
function createSingleSheetFile(): void
{
    $spreadsheet = new Spreadsheet();
    $challengesSheet = $spreadsheet->getActiveSheet();
    $challengesSheet->setTitle('Challenges');
    $challengesSheet->fromArray([
        'ID', 'Name', 'Short Description', 'Description', 'Image',
        'Max Points', 'Starts At', 'Expires At', 'Hint Text', 'Hint Image',
        'Skill Analytical', 'Skill StrategicPlanning', 'Skill Adaptability',
        'Skill PremierLeagueKnowledge', 'Skill RiskManagement',
        'Skill DecisionMakingUnderPressure', 'Skill FinancialManagement', 'Skill LongTermVision',
    ], null, 'A1');
    // Only one sheet - no Questions sheet!

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/challenge_import_single_sheet.xlsx');
}

// Invalid question type
function createInvalidQuestionTypeFile(): void
{
    $spreadsheet = createBaseSpreadsheet();
    $challengesSheet = $spreadsheet->getSheet(0);
    $challengesSheet->fromArray([
        'C001',
        'Test Challenge',
        'Short desc',
        'Full description',
        null,
        '100',
        '2024-01-01 00:00:00',
        '2024-12-31 23:59:59',
        null,
        null,
        '10', '10', '10', '10', '10', '10', '10', '10',
    ], null, 'A2');

    $questionsSheet = $spreadsheet->getSheet(1);
    $questionsSheet->fromArray([
        'C001',
        'Test question',
        'invalid_type', // Invalid QuestionType!
        null,
        null, null, null, null, null,
    ], null, 'A2');

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/challenge_import_invalid_question_type.xlsx');
}

// Invalid JSON choices
function createInvalidJsonChoicesFile(): void
{
    $spreadsheet = createBaseSpreadsheet();
    $challengesSheet = $spreadsheet->getSheet(0);
    $challengesSheet->fromArray([
        'C001',
        'Test Challenge',
        'Short desc',
        'Full description',
        null,
        '100',
        '2024-01-01 00:00:00',
        '2024-12-31 23:59:59',
        null,
        null,
        '10', '10', '10', '10', '10', '10', '10', '10',
    ], null, 'A2');

    $questionsSheet = $spreadsheet->getSheet(1);
    $questionsSheet->fromArray([
        'C001',
        'Test question',
        'single_select',
        null,
        null,
        null,
        '{this is not valid json}', // Invalid JSON!
        null,
        null,
    ], null, 'A2');

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/challenge_import_invalid_json_choices.xlsx');
}

// Import with correct answers (new challenge)
function createImportWithCorrectAnswersFile(): void
{
    $spreadsheet = createBaseSpreadsheetWithCorrectAnswers();

    $challengesSheet = $spreadsheet->getSheet(0);
    $challengesSheet->fromArray([
        'C003',
        'Correct Answer Test Challenge',
        'Testing correct answers',
        'This challenge tests importing questions with correct answers.',
        null,
        '1000',
        '2024-01-01 00:00:00',
        '2024-12-31 23:59:59',
        null,
        null,
        '20', '20', '20', '20', '20', '20', '20', '20',
    ], null, 'A2');

    $questionsSheet = $spreadsheet->getSheet(1);

    // Text question with correct answer
    $questionsSheet->fromArray([
        'C003',
        'What is your strategy?',
        'text',
        null,
        null,
        null,
        null,
        null,
        null,
        'Example text answer', // Correct text answer
        null,
        null,
        null,
        null,
    ], null, 'A2');

    // Numeric question with correct answer
    $questionsSheet->fromArray([
        'C003',
        'How many points do you expect?',
        'numeric',
        null,
        '0',
        '100',
        null,
        null,
        null,
        null,
        '42', // Correct numeric answer
        null,
        null,
        null,
    ], null, 'A3');

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/challenge_import_with_correct_answers.xlsx');
}

// Update existing challenge with correct answers
function createUpdateWithCorrectAnswersFile(): void
{
    $spreadsheet = createBaseSpreadsheetWithCorrectAnswers();

    // Use actual UUID from first import (C001 -> UUID 001)
    $challengesSheet = $spreadsheet->getSheet(0);
    $challengesSheet->fromArray([
        '01933333-0000-7000-8000-000000000001', // Actual UUID from first import
        'The Transfer Window Challenge',
        'Make smart transfers',
        'You have £100m to build your dream team. Choose wisely and maximize your points.',
        'https://example.com/transfer.jpg',
        '1000',
        '2024-01-01 00:00:00',
        '2024-12-31 23:59:59',
        'Consider player form and fixtures',
        'https://example.com/hint.jpg',
        '25',
        '30',
        '15',
        '40',
        '20',
        '25',
        '35',
        '10',
    ], null, 'A2');

    $questionsSheet = $spreadsheet->getSheet(1);

    // Single select question with correct answer
    $questionsSheet->fromArray([
        '01933333-0000-7000-8000-000000000001', // Challenge UUID
        'Who will you transfer in as your premium midfielder?',
        'single_select',
        null,
        null,
        null,
        json_encode([
            ['text' => 'Mohamed Salah', 'description' => 'Liverpool star with great form', 'image' => 'https://example.com/salah.jpg'],
            ['text' => 'Kevin De Bruyne', 'description' => 'Man City playmaker', 'image' => null],
            ['text' => 'Bruno Fernandes', 'description' => 'Manchester United captain'],
        ]),
        '1',
        '1',
        null,
        null,
        'Mohamed Salah', // Correct choice answer
        null,
        null,
    ], null, 'A2');

    // Multi select question
    $questionsSheet->fromArray([
        '01933333-0000-7000-8000-000000000001', // Challenge UUID
        'Select your defensive picks (choose 2)',
        'multi_select',
        null,
        null,
        null,
        json_encode([
            ['text' => 'William Saliba', 'description' => 'Arsenal defender'],
            ['text' => 'Virgil van Dijk', 'description' => 'Liverpool defender'],
            ['text' => 'Ruben Dias', 'description' => 'Man City defender'],
            ['text' => 'Cristian Romero', 'description' => 'Spurs defender'],
        ]),
        '2',
        '2',
        null,
        null,
        null,
        null,
        null,
    ], null, 'A3');

    // Numeric question with correct answer
    $questionsSheet->fromArray([
        '01933333-0000-7000-8000-000000000001', // Challenge UUID
        'How many goals will your team score this gameweek?',
        'numeric',
        null,
        '0',
        '50',
        null,
        null,
        null,
        null,
        '25', // Correct numeric answer
        null,
        null,
        null,
    ], null, 'A4');

    // Text question with correct answer
    $questionsSheet->fromArray([
        '01933333-0000-7000-8000-000000000001', // Challenge UUID
        'Explain your transfer strategy in one sentence',
        'text',
        'https://example.com/strategy.jpg',
        null,
        null,
        null,
        null,
        null,
        'Focus on form and fixtures', // Correct text answer
        null,
        null,
        null,
        null,
    ], null, 'A5');

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/challenge_import_update_with_correct_answers.xlsx');
}

// Create all test files
echo "Generating Excel test files...\n";

echo "Creating challenge_import_valid.xlsx...\n";
createValidFile();

echo "Creating challenge_import_missing_challenge_column.xlsx...\n";
createMissingChallengeColumnFile();

echo "Creating challenge_import_missing_question_column.xlsx...\n";
createMissingQuestionColumnFile();

echo "Creating challenge_import_duplicate_challenge_id.xlsx...\n";
createDuplicateChallengeIdFile();

echo "Creating challenge_import_invalid_challenge_reference.xlsx...\n";
createInvalidChallengeReferenceFile();

echo "Creating challenge_import_empty_challenge_id.xlsx...\n";
createEmptyChallengeIdFile();

echo "Creating challenge_import_empty_question_challenge_id.xlsx...\n";
createEmptyQuestionChallengeIdFile();

echo "Creating challenge_import_single_sheet.xlsx...\n";
createSingleSheetFile();

echo "Creating challenge_import_invalid_question_type.xlsx...\n";
createInvalidQuestionTypeFile();

echo "Creating challenge_import_invalid_json_choices.xlsx...\n";
createInvalidJsonChoicesFile();

echo "Creating challenge_import_with_correct_answers.xlsx...\n";
createImportWithCorrectAnswersFile();

echo "Creating challenge_import_update_with_correct_answers.xlsx...\n";
createUpdateWithCorrectAnswersFile();

echo "\n✅ All test files generated successfully!\n";
echo "Files created in: " . __DIR__ . "\n";
