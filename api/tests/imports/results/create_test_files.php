<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../vendor/autoload.php';

use FantasyAcademy\API\Tests\DataFixtures\PlayerChallengeAnswerFixture;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Valid import file with fixture data
function createValidFile(): void
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Points');

    // Headers
    $sheet->setCellValue('A1', 'id');
    $sheet->setCellValue('B1', 'points');

    // Data from fixtures - USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID with updated points
    $sheet->setCellValue('A2', PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID);
    $sheet->setCellValue('B2', 850); // Update from 800

    // USER_2_EXPIRED_CHALLENGE_1_ANSWER_ID with updated points
    $sheet->setCellValue('A3', PlayerChallengeAnswerFixture::USER_2_EXPIRED_CHALLENGE_1_ANSWER_ID);
    $sheet->setCellValue('B3', 950); // Update from 900

    // USER_3_EXPIRED_CHALLENGE_1_ANSWER_ID - same points
    $sheet->setCellValue('A4', PlayerChallengeAnswerFixture::USER_3_EXPIRED_CHALLENGE_1_ANSWER_ID);
    $sheet->setCellValue('B4', 900); // Keep same

    // USER_1_EXPIRED_CHALLENGE_2_ANSWER_ID
    $sheet->setCellValue('A5', PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_2_ANSWER_ID);
    $sheet->setCellValue('B5', 650); // Update from 600

    // Test formula calculation - USER_2_EXPIRED_CHALLENGE_2_ANSWER_ID
    $sheet->setCellValue('A6', PlayerChallengeAnswerFixture::USER_2_EXPIRED_CHALLENGE_2_ANSWER_ID);
    $sheet->setCellValue('B6', '=500+250'); // Should calculate to 750

    // USER_1_EXPIRED_CHALLENGE_3_ANSWER_ID (unevaluated challenge)
    $sheet->setCellValue('A7', PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_3_ANSWER_ID);
    $sheet->setCellValue('B7', 550); // Update from 500 to 550

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/results_import_valid.xlsx');
}

// Missing "Points" sheet
function createMissingSheetFile(): void
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Results'); // Wrong name

    $sheet->setCellValue('A1', 'id');
    $sheet->setCellValue('B1', 'points');

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/results_import_missing_sheet.xlsx');
}

// Missing required column
function createMissingColumnsFile(): void
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Points');

    // Missing "points" column
    $sheet->setCellValue('A1', 'id');

    $sheet->setCellValue('A2', PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID);

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/results_import_missing_columns.xlsx');
}

// Invalid UUIDs
function createInvalidUuidsFile(): void
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Points');

    $sheet->setCellValue('A1', 'id');
    $sheet->setCellValue('B1', 'points');

    // Invalid UUID in id column - should be skipped and added to missingIds
    $sheet->setCellValue('A2', 'not-a-valid-uuid');
    $sheet->setCellValue('B2', 100);

    // Valid row to ensure some data can be imported
    $sheet->setCellValue('A3', PlayerChallengeAnswerFixture::USER_2_EXPIRED_CHALLENGE_1_ANSWER_ID);
    $sheet->setCellValue('B3', 300);

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/results_import_invalid_uuids.xlsx');
}

// Mix of valid and missing IDs (should throw ImportResultsWarning)
function createWithWarningsFile(): void
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Points');

    $sheet->setCellValue('A1', 'id');
    $sheet->setCellValue('B1', 'points');

    // Valid existing answer
    $sheet->setCellValue('A2', PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID);
    $sheet->setCellValue('B2', 100);

    // Non-existent answer ID (valid UUID but doesn't exist)
    $sheet->setCellValue('A3', '99999999-9999-9999-9999-999999999999');
    $sheet->setCellValue('B3', 200);

    // Another valid existing answer
    $sheet->setCellValue('A4', PlayerChallengeAnswerFixture::USER_2_EXPIRED_CHALLENGE_1_ANSWER_ID);
    $sheet->setCellValue('B4', 300);

    // Another non-existent answer ID
    $sheet->setCellValue('A5', '88888888-8888-8888-8888-888888888888');
    $sheet->setCellValue('B5', 400);

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/results_import_with_warnings.xlsx');
}

// Empty/null rows
function createEmptyRowsFile(): void
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Points');

    $sheet->setCellValue('A1', 'id');
    $sheet->setCellValue('B1', 'points');

    // Empty row
    $sheet->setCellValue('A2', '');
    $sheet->setCellValue('B2', 100);

    // Null id
    $sheet->setCellValue('A3', null);
    $sheet->setCellValue('B3', 200);

    // Valid row
    $sheet->setCellValue('A4', PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID);
    $sheet->setCellValue('B4', 400);

    // Completely empty row
    $sheet->setCellValue('A5', null);
    $sheet->setCellValue('B5', null);

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/results_import_empty_rows.xlsx');
}

// Non-existent challenge ID (not needed anymore, but keeping for compatibility)
function createNonExistentChallengeFile(): void
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Points');

    $sheet->setCellValue('A1', 'id');
    $sheet->setCellValue('B1', 'points');

    // Valid answer ID
    $sheet->setCellValue('A2', PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID);
    $sheet->setCellValue('B2', 100);

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/results_import_nonexistent_challenge.xlsx');
}

// Multiple challenges in one import
function createMultipleChallengesFile(): void
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Points');

    $sheet->setCellValue('A1', 'id');
    $sheet->setCellValue('B1', 'points');

    // Challenge 1 answers
    $sheet->setCellValue('A2', PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID);
    $sheet->setCellValue('B2', 100);

    $sheet->setCellValue('A3', PlayerChallengeAnswerFixture::USER_2_EXPIRED_CHALLENGE_1_ANSWER_ID);
    $sheet->setCellValue('B3', 200);

    // Challenge 2 answers
    $sheet->setCellValue('A4', PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_2_ANSWER_ID);
    $sheet->setCellValue('B4', 300);

    $sheet->setCellValue('A5', PlayerChallengeAnswerFixture::USER_2_EXPIRED_CHALLENGE_2_ANSWER_ID);
    $sheet->setCellValue('B5', 400);

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/results_import_multiple_challenges.xlsx');
}

// Zero points test file
function createZeroPointsFile(): void
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Points');

    $sheet->setCellValue('A1', 'id');
    $sheet->setCellValue('B1', 'points');

    $sheet->setCellValue('A2', PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID);
    $sheet->setCellValue('B2', 0);

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__ . '/results_import_zero_points.xlsx');
}

// Create all files
createValidFile();
createMissingSheetFile();
createMissingColumnsFile();
createInvalidUuidsFile();
createWithWarningsFile();
createEmptyRowsFile();
createNonExistentChallengeFile();
createMultipleChallengesFile();
createZeroPointsFile();

echo "Test files created successfully!\n";
