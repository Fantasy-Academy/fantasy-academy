<?php

declare(strict_types=1);

/**
 * Script to generate Excel test files for ChallengesImport tests.
 * Run from project root: docker compose exec api php tests/imports/challenge/generate_test_files.php
 */

require __DIR__ . '/../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$outputDir = __DIR__;

echo "Generating Excel test files...\n";

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

// 1. Valid import file
echo "Creating challenge_import_valid.xlsx...\n";
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
$writer->save($outputDir . '/challenge_import_valid.xlsx');

// 2. Missing challenge column
echo "Creating challenge_import_missing_challenge_column.xlsx...\n";
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
$writer->save($outputDir . '/challenge_import_missing_challenge_column.xlsx');

// 3. Missing question column
echo "Creating challenge_import_missing_question_column.xlsx...\n";
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
$writer->save($outputDir . '/challenge_import_missing_question_column.xlsx');

// 4. Duplicate challenge ID
echo "Creating challenge_import_duplicate_challenge_id.xlsx...\n";
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
$writer->save($outputDir . '/challenge_import_duplicate_challenge_id.xlsx');

// 5. Invalid challenge reference
echo "Creating challenge_import_invalid_challenge_reference.xlsx...\n";
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
$writer->save($outputDir . '/challenge_import_invalid_challenge_reference.xlsx');

// 6. Empty challenge ID
echo "Creating challenge_import_empty_challenge_id.xlsx...\n";
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
$writer->save($outputDir . '/challenge_import_empty_challenge_id.xlsx');

// 7. Empty question challenge ID
echo "Creating challenge_import_empty_question_challenge_id.xlsx...\n";
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
$writer->save($outputDir . '/challenge_import_empty_question_challenge_id.xlsx');

// 8. Single sheet only
echo "Creating challenge_import_single_sheet.xlsx...\n";
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
$writer->save($outputDir . '/challenge_import_single_sheet.xlsx');

// 9. Invalid question type
echo "Creating challenge_import_invalid_question_type.xlsx...\n";
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
$writer->save($outputDir . '/challenge_import_invalid_question_type.xlsx');

// 10. Invalid JSON choices
echo "Creating challenge_import_invalid_json_choices.xlsx...\n";
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
$writer->save($outputDir . '/challenge_import_invalid_json_choices.xlsx');

echo "\n✅ All test files generated successfully!\n";
echo "Files created in: $outputDir\n";
