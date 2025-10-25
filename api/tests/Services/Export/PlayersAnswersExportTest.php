<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Services\Export;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Services\Export\PlayersAnswersExport;
use FantasyAcademy\API\Tests\DataFixtures\CurrentChallenge1Fixture;
use FantasyAcademy\API\Tests\DataFixtures\CurrentChallenge2Fixture;
use FantasyAcademy\API\Tests\DataFixtures\ExpiredChallenge2Fixture;
use FantasyAcademy\API\Tests\DataFixtures\ExpiredChallengeFixture;
use FantasyAcademy\API\Tests\DataFixtures\PlayerChallengeAnswerFixture;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;

final class PlayersAnswersExportTest extends ApiTestCase
{
    private PlayersAnswersExport $exporter;

    protected function setUp(): void
    {
        $client = self::createClient();
        $container = $client->getContainer();

        /** @var PlayersAnswersExport $exporter */
        $exporter = $container->get(PlayersAnswersExport::class);
        $this->exporter = $exporter;
    }

    public function testExportCreatesSpreadsheetWithTwoSheets(): void
    {
        $spreadsheet = $this->exporter->exportAnswers([ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID]);

        $this->assertCount(2, $spreadsheet->getAllSheets());
        $this->assertSame('Points', $spreadsheet->getSheet(0)->getTitle());
        $this->assertSame('Answers', $spreadsheet->getSheet(1)->getTitle());
    }

    public function testPointsSheetHasCorrectHeaders(): void
    {
        $spreadsheet = $this->exporter->exportAnswers([ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID]);
        $pointsSheet = $spreadsheet->getSheet(0);

        $this->assertSame('id', $pointsSheet->getCell('A1')->getValue());
        $this->assertSame('user_id', $pointsSheet->getCell('B1')->getValue());
        $this->assertSame('challenge_id', $pointsSheet->getCell('C1')->getValue());
        $this->assertSame('name', $pointsSheet->getCell('D1')->getValue());
        $this->assertSame('points', $pointsSheet->getCell('E1')->getValue());
    }

    public function testAnswersSheetHasCorrectHeaders(): void
    {
        $spreadsheet = $this->exporter->exportAnswers([ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID]);
        $answersSheet = $spreadsheet->getSheet(1);

        $this->assertSame('player_id', $answersSheet->getCell('A1')->getValue());
        $this->assertSame('challenge_id', $answersSheet->getCell('B1')->getValue());
        $this->assertSame('question_id', $answersSheet->getCell('C1')->getValue());
        $this->assertSame('question_name', $answersSheet->getCell('D1')->getValue());
        $this->assertSame('text_answer', $answersSheet->getCell('E1')->getValue());
        $this->assertSame('numeric_answer', $answersSheet->getCell('F1')->getValue());
        $this->assertSame('selected_choice_text', $answersSheet->getCell('G1')->getValue());
        $this->assertSame('selected_choice_texts', $answersSheet->getCell('H1')->getValue());
        $this->assertSame('ordered_choice_texts', $answersSheet->getCell('I1')->getValue());
        $this->assertSame('challenge_answer_id', $answersSheet->getCell('J1')->getValue());
    }

    public function testPointsSheetContainsCorrectData(): void
    {
        $spreadsheet = $this->exporter->exportAnswers([ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]);
        $pointsSheet = $spreadsheet->getSheet(0);

        // User1 answer (800 points)
        $this->assertSame(PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID, $pointsSheet->getCell('A2')->getValue());
        $this->assertSame(UserFixture::USER_1_ID, $pointsSheet->getCell('B2')->getValue());
        $this->assertSame(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID, $pointsSheet->getCell('C2')->getValue());
        $this->assertSame('Some expired challenge', $pointsSheet->getCell('D2')->getValue());
        $this->assertSame(800, $pointsSheet->getCell('E2')->getValue());

        // User2 answer (900 points)
        $this->assertSame(PlayerChallengeAnswerFixture::USER_2_EXPIRED_CHALLENGE_1_ANSWER_ID, $pointsSheet->getCell('A3')->getValue());
        $this->assertSame(UserFixture::USER_2_ID, $pointsSheet->getCell('B3')->getValue());
        $this->assertSame(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID, $pointsSheet->getCell('C3')->getValue());
        $this->assertSame('Some expired challenge', $pointsSheet->getCell('D3')->getValue());
        $this->assertSame(900, $pointsSheet->getCell('E3')->getValue());

        // User3 answer (900 points)
        $this->assertSame(PlayerChallengeAnswerFixture::USER_3_EXPIRED_CHALLENGE_1_ANSWER_ID, $pointsSheet->getCell('A4')->getValue());
        $this->assertSame(UserFixture::USER_3_ID, $pointsSheet->getCell('B4')->getValue());
        $this->assertSame(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID, $pointsSheet->getCell('C4')->getValue());
        $this->assertSame('Some expired challenge', $pointsSheet->getCell('D4')->getValue());
        $this->assertSame(900, $pointsSheet->getCell('E4')->getValue());
    }

    public function testAnswersSheetContainsTextAnswerData(): void
    {
        $spreadsheet = $this->exporter->exportAnswers([ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]);
        $answersSheet = $spreadsheet->getSheet(1);

        // User1's text answer
        $this->assertSame(UserFixture::USER_1_ID, $answersSheet->getCell('A2')->getValue());
        $this->assertSame(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID, $answersSheet->getCell('B2')->getValue());
        $this->assertSame(ExpiredChallengeFixture::QUESTION_7_ID, $answersSheet->getCell('C2')->getValue());
        $this->assertSame('Some dummy expired question', $answersSheet->getCell('D2')->getValue());
        $this->assertSame('User 1 answer to question 7', $answersSheet->getCell('E2')->getValue());
        $this->assertNull($answersSheet->getCell('F2')->getValue());
        $this->assertNull($answersSheet->getCell('G2')->getValue());
        $this->assertNull($answersSheet->getCell('H2')->getValue());
        $this->assertNull($answersSheet->getCell('I2')->getValue());
        $this->assertSame(PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID, $answersSheet->getCell('J2')->getValue());
    }

    public function testAnswersSheetWithNumericAnswer(): void
    {
        $spreadsheet = $this->exporter->exportAnswers([ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID]);
        $answersSheet = $spreadsheet->getSheet(1);

        // Find a row with question 10 (numeric question)
        $found = false;
        for ($row = 2; $row <= 20; $row++) {
            if ($answersSheet->getCell("C{$row}")->getValue() === ExpiredChallenge2Fixture::QUESTION_10_ID) {
                $this->assertSame('Enter a number between 1 and 100', $answersSheet->getCell("D{$row}")->getValue());
                $this->assertNull($answersSheet->getCell("E{$row}")->getValue());
                $this->assertIsFloat($answersSheet->getCell("F{$row}")->getValue());
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Should find at least one numeric answer for question 10');
    }

    public function testAnswersSheetWithSingleSelectChoiceAnswer(): void
    {
        $spreadsheet = $this->exporter->exportAnswers([ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID]);
        $answersSheet = $spreadsheet->getSheet(1);

        // User1 selected "Red" (CHOICE_9_ID) for question 8 (row 2)
        $this->assertSame(UserFixture::USER_1_ID, $answersSheet->getCell('A2')->getValue());
        $this->assertSame(ExpiredChallenge2Fixture::QUESTION_8_ID, $answersSheet->getCell('C2')->getValue());
        $this->assertSame('What is your favorite color?', $answersSheet->getCell('D2')->getValue());
        $this->assertSame('Red', $answersSheet->getCell('G2')->getValue());
    }

    public function testAnswersSheetWithMultiSelectChoiceAnswers(): void
    {
        $spreadsheet = $this->exporter->exportAnswers([ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID]);
        $answersSheet = $spreadsheet->getSheet(1);

        // Find a row with question 9 (multi-select question) that has multiple choices
        $found = false;
        for ($row = 2; $row <= 20; $row++) {
            if ($answersSheet->getCell("C$row")->getValue() === ExpiredChallenge2Fixture::QUESTION_9_ID) {
                $selectedTexts = $answersSheet->getCell("H$row")->getValue();
                if ($selectedTexts !== null) {
                    $this->assertIsString($selectedTexts);
                    // Check that it contains at least one of the choice texts
                    $this->assertTrue(
                        str_contains($selectedTexts, '7') || str_contains($selectedTexts, '13'),
                        'Multi-select answer should contain choice text'
                    );
                    $found = true;
                    break;
                }
            }
        }
        $this->assertTrue($found, 'Should find at least one multi-select answer for question 9');
    }

    public function testExportWithMultipleChallenges(): void
    {
        $spreadsheet = $this->exporter->exportAnswers([
            ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID,
            ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID,
        ]);
        $pointsSheet = $spreadsheet->getSheet(0);

        // Verify both challenges are in the export
        // First challenge answers (rows 2-4)
        $this->assertSame(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID, $pointsSheet->getCell('C2')->getValue());
        $this->assertSame('Some expired challenge', $pointsSheet->getCell('D2')->getValue());
        $this->assertSame(800, $pointsSheet->getCell('E2')->getValue());

        $this->assertSame(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID, $pointsSheet->getCell('C3')->getValue());
        $this->assertSame('Some expired challenge', $pointsSheet->getCell('D3')->getValue());
        $this->assertSame(900, $pointsSheet->getCell('E3')->getValue());

        // Second challenge answers (rows 5-7)
        $this->assertSame(ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID, $pointsSheet->getCell('C5')->getValue());
        $this->assertSame('Another expired challenge', $pointsSheet->getCell('D5')->getValue());
        $this->assertSame(600, $pointsSheet->getCell('E5')->getValue());

        $this->assertSame(ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID, $pointsSheet->getCell('C6')->getValue());
        $this->assertSame('Another expired challenge', $pointsSheet->getCell('D6')->getValue());
        $this->assertSame(700, $pointsSheet->getCell('E6')->getValue());
    }

    public function testExportWithEmptyData(): void
    {
        $spreadsheet = $this->exporter->exportAnswers([CurrentChallenge2Fixture::CURRENT_CHALLENGE_2_ID]);
        $pointsSheet = $spreadsheet->getSheet(0);
        $answersSheet = $spreadsheet->getSheet(1);

        // Verify headers exist but no data rows
        $this->assertSame('id', $pointsSheet->getCell('A1')->getValue());
        $this->assertNull($pointsSheet->getCell('A2')->getValue());

        $this->assertSame('player_id', $answersSheet->getCell('A1')->getValue());
        $this->assertNull($answersSheet->getCell('A2')->getValue());
    }

    public function testExportIncludesNonEvaluatedAnswers(): void
    {
        $spreadsheet = $this->exporter->exportAnswers([CurrentChallenge1Fixture::CURRENT_CHALLENGE_1_ID]);
        $pointsSheet = $spreadsheet->getSheet(0);
        $answersSheet = $spreadsheet->getSheet(1);

        // Verify User3's non-evaluated answer in Points sheet
        $foundInPoints = false;
        for ($row = 2; $row <= 20; $row++) {
            if ($pointsSheet->getCell("A$row")->getValue() === PlayerChallengeAnswerFixture::USER_3_CURRENT_CHALLENGE_1_ANSWER_ID) {
                $foundInPoints = true;
                $this->assertSame(UserFixture::USER_3_ID, $pointsSheet->getCell("B$row")->getValue());
                $this->assertSame(CurrentChallenge1Fixture::CURRENT_CHALLENGE_1_ID, $pointsSheet->getCell("C$row")->getValue());

                // Assert points is NULL for non-evaluated answer
                $points = $pointsSheet->getCell("E$row")->getValue();
                $this->assertNull($points, 'Non-evaluated answer should have NULL points');
                break;
            }
        }
        $this->assertTrue($foundInPoints, 'Should find User3 non-evaluated answer in Points sheet');

        // Verify User3's question answers ARE in Answers sheet
        $questionAnswersFound = 0;
        for ($row = 2; $row <= 50; $row++) {
            if ($answersSheet->getCell("J$row")->getValue() === PlayerChallengeAnswerFixture::USER_3_CURRENT_CHALLENGE_1_ANSWER_ID) {
                $questionAnswersFound++;
            }
        }
        $this->assertSame(3, $questionAnswersFound, 'Should have 3 question answers for User3 CurrentChallenge1');
    }
}
