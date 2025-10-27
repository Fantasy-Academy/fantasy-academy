<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Services\Export;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Services\Export\ChallengesExport;
use FantasyAcademy\API\Tests\DataFixtures\CurrentChallenge1Fixture;
use FantasyAcademy\API\Tests\DataFixtures\CurrentChallenge2Fixture;
use FantasyAcademy\API\Tests\DataFixtures\ExpiredChallenge2Fixture;
use FantasyAcademy\API\Tests\DataFixtures\ExpiredChallengeFixture;

final class ChallengesExportTest extends ApiTestCase
{
    private ChallengesExport $exporter;

    protected function setUp(): void
    {
        $client = self::createClient();
        $container = $client->getContainer();

        /** @var ChallengesExport $exporter */
        $exporter = $container->get(ChallengesExport::class);
        $this->exporter = $exporter;
    }

    public function testExportCreatesSpreadsheetWithTwoSheets(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]);

        $this->assertCount(2, $spreadsheet->getAllSheets());
        $this->assertSame('Challenges', $spreadsheet->getSheet(0)->getTitle());
        $this->assertSame('Questions', $spreadsheet->getSheet(1)->getTitle());
    }

    public function testChallengesSheetHasCorrectHeaders(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]);
        $challengesSheet = $spreadsheet->getSheet(0);

        $this->assertSame('id', $challengesSheet->getCell('A1')->getValue());
        $this->assertSame('name', $challengesSheet->getCell('B1')->getValue());
        $this->assertSame('short_description', $challengesSheet->getCell('C1')->getValue());
        $this->assertSame('description', $challengesSheet->getCell('D1')->getValue());
        $this->assertSame('image', $challengesSheet->getCell('E1')->getValue());
        $this->assertSame('max_points', $challengesSheet->getCell('F1')->getValue());
        $this->assertSame('starts_at', $challengesSheet->getCell('G1')->getValue());
        $this->assertSame('expires_at', $challengesSheet->getCell('H1')->getValue());
        $this->assertSame('hint_text', $challengesSheet->getCell('I1')->getValue());
        $this->assertSame('hint_image', $challengesSheet->getCell('J1')->getValue());
        $this->assertSame('show_statistics_continuously', $challengesSheet->getCell('K1')->getValue());
        $this->assertSame('gameweek', $challengesSheet->getCell('L1')->getValue());
        $this->assertSame('skill_analytical', $challengesSheet->getCell('M1')->getValue());
        $this->assertSame('skill_strategicplanning', $challengesSheet->getCell('N1')->getValue());
        $this->assertSame('skill_adaptability', $challengesSheet->getCell('O1')->getValue());
        $this->assertSame('skill_premierleagueknowledge', $challengesSheet->getCell('P1')->getValue());
        $this->assertSame('skill_riskmanagement', $challengesSheet->getCell('Q1')->getValue());
        $this->assertSame('skill_decisionmakingunderpressure', $challengesSheet->getCell('R1')->getValue());
        $this->assertSame('skill_financialmanagement', $challengesSheet->getCell('S1')->getValue());
        $this->assertSame('skill_longtermvision', $challengesSheet->getCell('T1')->getValue());
    }

    public function testQuestionsSheetHasCorrectHeaders(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]);
        $questionsSheet = $spreadsheet->getSheet(1);

        $this->assertSame('challenge_id', $questionsSheet->getCell('A1')->getValue());
        $this->assertSame('text', $questionsSheet->getCell('B1')->getValue());
        $this->assertSame('type', $questionsSheet->getCell('C1')->getValue());
        $this->assertSame('image', $questionsSheet->getCell('D1')->getValue());
        $this->assertSame('numeric_type_min', $questionsSheet->getCell('E1')->getValue());
        $this->assertSame('numeric_type_max', $questionsSheet->getCell('F1')->getValue());
        $this->assertSame('choices', $questionsSheet->getCell('G1')->getValue());
        $this->assertSame('choices_min_selections', $questionsSheet->getCell('H1')->getValue());
        $this->assertSame('choices_max_selections', $questionsSheet->getCell('I1')->getValue());
        $this->assertSame('correct_text_answer', $questionsSheet->getCell('J1')->getValue());
        $this->assertSame('correct_numeric_answer', $questionsSheet->getCell('K1')->getValue());
        $this->assertSame('correct_selected_choice_text', $questionsSheet->getCell('L1')->getValue());
        $this->assertSame('correct_selected_choice_texts', $questionsSheet->getCell('M1')->getValue());
        $this->assertSame('correct_ordered_choice_texts', $questionsSheet->getCell('N1')->getValue());
    }

    public function testChallengesSheetContainsChallengeData(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]);
        $challengesSheet = $spreadsheet->getSheet(0);

        // Verify challenge data in row 2
        $this->assertSame(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID, $challengesSheet->getCell('A2')->getValue());
        $this->assertSame('Some expired challenge', $challengesSheet->getCell('B2')->getValue());
        $this->assertIsString($challengesSheet->getCell('C2')->getValue()); // short_description
        $this->assertIsString($challengesSheet->getCell('D2')->getValue()); // description
        $this->assertIsInt($challengesSheet->getCell('F2')->getValue()); // max_points
        $this->assertIsString($challengesSheet->getCell('G2')->getValue()); // starts_at (formatted date)
        $this->assertIsString($challengesSheet->getCell('H2')->getValue()); // expires_at (formatted date)
    }

    public function testChallengesSheetContainsSkillData(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]);
        $challengesSheet = $spreadsheet->getSheet(0);

        // Verify skill values are floats
        $this->assertIsFloat($challengesSheet->getCell('M2')->getValue()); // skill_analytical
        $this->assertIsFloat($challengesSheet->getCell('N2')->getValue()); // skill_strategicplanning
        $this->assertIsFloat($challengesSheet->getCell('O2')->getValue()); // skill_adaptability
        $this->assertIsFloat($challengesSheet->getCell('P2')->getValue()); // skill_premierleagueknowledge
        $this->assertIsFloat($challengesSheet->getCell('Q2')->getValue()); // skill_riskmanagement
        $this->assertIsFloat($challengesSheet->getCell('R2')->getValue()); // skill_decisionmakingunderpressure
        $this->assertIsFloat($challengesSheet->getCell('S2')->getValue()); // skill_financialmanagement
        $this->assertIsFloat($challengesSheet->getCell('T2')->getValue()); // skill_longtermvision
    }

    public function testChallengesSheetContainsGameweek(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]);
        $challengesSheet = $spreadsheet->getSheet(0);

        $value = $challengesSheet->getCell('L2')->getValue();
        // Gameweek can be null or an integer
        $this->assertTrue($value === null || is_int($value), 'gameweek must be null or int');
    }

    public function testChallengesSheetContainsStatisticsContinuouslyFlag(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]);
        $challengesSheet = $spreadsheet->getSheet(0);

        $value = $challengesSheet->getCell('K2')->getValue();
        $this->assertContains($value, [0, 1], 'show_statistics_continuously must be 0 or 1');
    }

    public function testQuestionsSheetContainsQuestionData(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]);
        $questionsSheet = $spreadsheet->getSheet(1);

        // Verify at least one question exists (row 2)
        $this->assertSame(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID, $questionsSheet->getCell('A2')->getValue());
        $this->assertIsString($questionsSheet->getCell('B2')->getValue()); // question text
        $this->assertIsString($questionsSheet->getCell('C2')->getValue()); // question type
    }

    public function testQuestionsSheetContainsTextQuestionWithCorrectAnswer(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]);
        $questionsSheet = $spreadsheet->getSheet(1);

        // Find the text question row (Question 7)
        $foundTextQuestion = false;
        for ($row = 2; $row <= 10; $row++) {
            $questionType = $questionsSheet->getCell("C{$row}")->getValue();
            if ($questionType === 'text') {
                $correctAnswer = $questionsSheet->getCell("J{$row}")->getValue();
                if ($correctAnswer !== null) {
                    $this->assertIsString($correctAnswer);
                    $this->assertEquals('This is the correct text answer', $correctAnswer);
                    $foundTextQuestion = true;
                    break;
                }
            }
        }

        $this->assertTrue($foundTextQuestion, 'Should find at least one text question with correct answer');
    }

    public function testExportWithMultipleChallenges(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([
            ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID,
            CurrentChallenge1Fixture::CURRENT_CHALLENGE_1_ID,
        ]);
        $challengesSheet = $spreadsheet->getSheet(0);

        // Verify both challenges are in the export
        $this->assertSame(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID, $challengesSheet->getCell('A2')->getValue());
        $this->assertSame('Some expired challenge', $challengesSheet->getCell('B2')->getValue());

        $this->assertSame(CurrentChallenge1Fixture::CURRENT_CHALLENGE_1_ID, $challengesSheet->getCell('A3')->getValue());
        $this->assertSame('Some exciting challenge', $challengesSheet->getCell('B3')->getValue());
    }

    public function testQuestionsSheetContainsQuestionsFromMultipleChallenges(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([
            ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID,
            CurrentChallenge2Fixture::CURRENT_CHALLENGE_2_ID,
        ]);
        $questionsSheet = $spreadsheet->getSheet(1);

        // Count questions from each challenge
        $expiredChallengeQuestions = 0;
        $currentChallenge2Questions = 0;

        for ($row = 2; $row <= 100; $row++) {
            $challengeId = $questionsSheet->getCell("A{$row}")->getValue();
            if ($challengeId === null) {
                break;
            }

            if ($challengeId === ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID) {
                $expiredChallengeQuestions++;
            } elseif ($challengeId === CurrentChallenge2Fixture::CURRENT_CHALLENGE_2_ID) {
                $currentChallenge2Questions++;
            }
        }

        $this->assertGreaterThan(0, $expiredChallengeQuestions, 'Should have questions from ExpiredChallenge');
        $this->assertGreaterThan(0, $currentChallenge2Questions, 'Should have questions from CurrentChallenge2');
    }

    public function testQuestionsSheetContainsNumericQuestionWithCorrectAnswer(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID]);
        $questionsSheet = $spreadsheet->getSheet(1);

        // Find the numeric question row (Question 10)
        $foundNumericQuestion = false;
        for ($row = 2; $row <= 10; $row++) {
            $questionId = $questionsSheet->getCell("A{$row}")->getValue();
            if ($questionId === ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID) {
                $questionType = $questionsSheet->getCell("C{$row}")->getValue();
                if ($questionType === 'numeric') {
                    $correctAnswer = $questionsSheet->getCell("K{$row}")->getValue();
                    $this->assertNotNull($correctAnswer, 'Numeric question should have correct answer');
                    $this->assertIsFloat($correctAnswer);
                    $this->assertEquals(42.0, $correctAnswer);
                    $foundNumericQuestion = true;
                    break;
                }
            }
        }

        $this->assertTrue($foundNumericQuestion, 'Should find at least one numeric question with correct answer');
    }

    public function testQuestionsSheetContainsSingleSelectQuestionWithCorrectAnswer(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID]);
        $questionsSheet = $spreadsheet->getSheet(1);

        // Find the single-select question row (Question 8)
        $foundSingleSelectQuestion = false;
        for ($row = 2; $row <= 10; $row++) {
            $questionId = $questionsSheet->getCell("A{$row}")->getValue();
            if ($questionId === ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID) {
                $questionType = $questionsSheet->getCell("C{$row}")->getValue();
                if ($questionType === 'single_select') {
                    $correctAnswer = $questionsSheet->getCell("L{$row}")->getValue();
                    $this->assertNotNull($correctAnswer, 'Single-select question should have correct answer');
                    $this->assertIsString($correctAnswer);
                    $this->assertEquals('Red', $correctAnswer);
                    $foundSingleSelectQuestion = true;
                    break;
                }
            }
        }

        $this->assertTrue($foundSingleSelectQuestion, 'Should find at least one single-select question with correct answer');
    }

    public function testQuestionsSheetContainsMultiSelectQuestionWithCorrectAnswer(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID]);
        $questionsSheet = $spreadsheet->getSheet(1);

        // Find the multi-select question row (Question 9)
        $foundMultiSelectQuestion = false;
        for ($row = 2; $row <= 10; $row++) {
            $questionId = $questionsSheet->getCell("A{$row}")->getValue();
            if ($questionId === ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID) {
                $questionType = $questionsSheet->getCell("C{$row}")->getValue();
                if ($questionType === 'multi_select') {
                    $correctAnswer = $questionsSheet->getCell("M{$row}")->getValue();
                    $this->assertNotNull($correctAnswer, 'Multi-select question should have correct answer');
                    $this->assertIsString($correctAnswer);
                    // Verify it's valid JSON
                    $this->assertTrue(json_validate($correctAnswer), 'Correct answer must be valid JSON');
                    // Parse and verify contents
                    $parsedAnswer = json_decode($correctAnswer, true);
                    $this->assertIsArray($parsedAnswer);
                    $this->assertCount(2, $parsedAnswer);
                    $this->assertContains('7', $parsedAnswer);
                    $this->assertContains('13', $parsedAnswer);
                    $foundMultiSelectQuestion = true;
                    break;
                }
            }
        }

        $this->assertTrue($foundMultiSelectQuestion, 'Should find at least one multi-select question with correct answer');
    }

    public function testQuestionsSheetChoicesFieldIsValidJSON(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID]);
        $questionsSheet = $spreadsheet->getSheet(1);

        // Find a question with choices (Question 8 - single select)
        $foundChoiceQuestion = false;
        for ($row = 2; $row <= 10; $row++) {
            $questionId = $questionsSheet->getCell("A{$row}")->getValue();
            if ($questionId === ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID) {
                $questionType = $questionsSheet->getCell("C{$row}")->getValue();
                if ($questionType === 'single_select' || $questionType === 'multi_select') {
                    $choicesJson = $questionsSheet->getCell("G{$row}")->getValue();
                    $this->assertNotNull($choicesJson, 'Choices field should not be null for choice questions');
                    $this->assertIsString($choicesJson);

                    // Verify it's valid JSON
                    $this->assertTrue(json_validate($choicesJson), 'Choices must be valid JSON');

                    // Parse and verify structure
                    $choices = json_decode($choicesJson, true);
                    $this->assertIsArray($choices);
                    $this->assertGreaterThan(0, count($choices));

                    // Verify each choice has required fields
                    foreach ($choices as $choice) {
                        $this->assertIsArray($choice);
                        $this->assertArrayHasKey('text', $choice);
                        $this->assertArrayHasKey('description', $choice);
                        $this->assertArrayHasKey('image', $choice);
                        $this->assertIsString($choice['text']);
                    }

                    $foundChoiceQuestion = true;
                    break;
                }
            }
        }

        $this->assertTrue($foundChoiceQuestion, 'Should find at least one question with choices');
    }

    public function testHeadersMatchTemplateExportFormat(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]);
        $challengesSheet = $spreadsheet->getSheet(0);
        $questionsSheet = $spreadsheet->getSheet(1);

        // Extract all headers from Challenges sheet
        $challengeHeaders = [];
        for ($col = 'A'; $col <= 'T'; $col++) {
            $header = $challengesSheet->getCell($col . '1')->getValue();
            if ($header !== null) {
                $challengeHeaders[] = $header;
            }
        }

        // Extract all headers from Questions sheet
        $questionHeaders = [];
        for ($col = 'A'; $col <= 'N'; $col++) {
            $header = $questionsSheet->getCell($col . '1')->getValue();
            if ($header !== null) {
                $questionHeaders[] = $header;
            }
        }

        // Verify challenge headers match template export format
        $expectedChallengeHeaders = [
            'id', 'name', 'short_description', 'description', 'image', 'max_points',
            'starts_at', 'expires_at', 'hint_text', 'hint_image', 'show_statistics_continuously', 'gameweek',
            'skill_analytical', 'skill_strategicplanning', 'skill_adaptability',
            'skill_premierleagueknowledge', 'skill_riskmanagement',
            'skill_decisionmakingunderpressure', 'skill_financialmanagement', 'skill_longtermvision',
        ];
        $this->assertSame($expectedChallengeHeaders, $challengeHeaders);

        // Verify question headers match template export format
        $expectedQuestionHeaders = [
            'challenge_id', 'text', 'type', 'image', 'numeric_type_min', 'numeric_type_max',
            'choices', 'choices_min_selections', 'choices_max_selections',
            'correct_text_answer', 'correct_numeric_answer', 'correct_selected_choice_text',
            'correct_selected_choice_texts', 'correct_ordered_choice_texts',
        ];
        $this->assertSame($expectedQuestionHeaders, $questionHeaders);
    }

    public function testHeadersAreNormalizedFormat(): void
    {
        $spreadsheet = $this->exporter->exportChallenges([ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID]);
        $challengesSheet = $spreadsheet->getSheet(0);
        $questionsSheet = $spreadsheet->getSheet(1);

        // All headers should be lowercase with underscores (snake_case)
        for ($col = 'A'; $col <= 'T'; $col++) {
            $header = $challengesSheet->getCell($col . '1')->getValue();
            if (is_string($header)) {
                $this->assertMatchesRegularExpression(
                    '/^[a-z0-9_]+$/',
                    $header,
                    sprintf('Challenge header "%s" must be normalized (lowercase, underscores)', $header)
                );
            }
        }

        for ($col = 'A'; $col <= 'N'; $col++) {
            $header = $questionsSheet->getCell($col . '1')->getValue();
            if (is_string($header)) {
                $this->assertMatchesRegularExpression(
                    '/^[a-z0-9_]+$/',
                    $header,
                    sprintf('Question header "%s" must be normalized (lowercase, underscores)', $header)
                );
            }
        }
    }
}
