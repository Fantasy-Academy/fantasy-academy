<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Services\Export;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Services\Export\ChallengeTemplateExport;

final class ChallengeTemplateExportTest extends ApiTestCase
{
    private ChallengeTemplateExport $exporter;

    protected function setUp(): void
    {
        $client = self::createClient();
        $container = $client->getContainer();

        /** @var ChallengeTemplateExport $exporter */
        $exporter = $container->get(ChallengeTemplateExport::class);
        $this->exporter = $exporter;
    }

    public function testExportCreatesSpreadsheetWithTwoSheets(): void
    {
        $spreadsheet = $this->exporter->exportTemplate();

        $this->assertCount(2, $spreadsheet->getAllSheets());
        $this->assertSame('Challenges', $spreadsheet->getSheet(0)->getTitle());
        $this->assertSame('Questions', $spreadsheet->getSheet(1)->getTitle());
    }

    public function testChallengesSheetHasCorrectHeaders(): void
    {
        $spreadsheet = $this->exporter->exportTemplate();
        $challengesSheet = $spreadsheet->getSheet(0);

        // Verify all required challenge headers match import structure
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
        $this->assertSame('skill_analytical', $challengesSheet->getCell('L1')->getValue());
        $this->assertSame('skill_strategicplanning', $challengesSheet->getCell('M1')->getValue());
        $this->assertSame('skill_adaptability', $challengesSheet->getCell('N1')->getValue());
        $this->assertSame('skill_premierleagueknowledge', $challengesSheet->getCell('O1')->getValue());
        $this->assertSame('skill_riskmanagement', $challengesSheet->getCell('P1')->getValue());
        $this->assertSame('skill_decisionmakingunderpressure', $challengesSheet->getCell('Q1')->getValue());
        $this->assertSame('skill_financialmanagement', $challengesSheet->getCell('R1')->getValue());
        $this->assertSame('skill_longtermvision', $challengesSheet->getCell('S1')->getValue());
    }

    public function testQuestionsSheetHasCorrectHeaders(): void
    {
        $spreadsheet = $this->exporter->exportTemplate();
        $questionsSheet = $spreadsheet->getSheet(1);

        // Verify all required question headers match import structure
        $this->assertSame('challenge_id', $questionsSheet->getCell('A1')->getValue());
        $this->assertSame('text', $questionsSheet->getCell('B1')->getValue());
        $this->assertSame('type', $questionsSheet->getCell('C1')->getValue());
        $this->assertSame('image', $questionsSheet->getCell('D1')->getValue());
        $this->assertSame('numeric_type_min', $questionsSheet->getCell('E1')->getValue());
        $this->assertSame('numeric_type_max', $questionsSheet->getCell('F1')->getValue());
        $this->assertSame('choices', $questionsSheet->getCell('G1')->getValue());
        $this->assertSame('choices_min_selections', $questionsSheet->getCell('H1')->getValue());
        $this->assertSame('choices_max_selections', $questionsSheet->getCell('I1')->getValue());
    }

    public function testChallengesSheetHasNoDataRows(): void
    {
        $spreadsheet = $this->exporter->exportTemplate();
        $challengesSheet = $spreadsheet->getSheet(0);

        // Verify there are no data rows, only headers
        $this->assertNull($challengesSheet->getCell('A2')->getValue());
        $this->assertNull($challengesSheet->getCell('B2')->getValue());
    }

    public function testQuestionsSheetHasNoDataRows(): void
    {
        $spreadsheet = $this->exporter->exportTemplate();
        $questionsSheet = $spreadsheet->getSheet(1);

        // Verify there are no data rows, only headers
        $this->assertNull($questionsSheet->getCell('A2')->getValue());
        $this->assertNull($questionsSheet->getCell('B2')->getValue());
    }

    public function testChallengesHeadersMatchImportRequirements(): void
    {
        $spreadsheet = $this->exporter->exportTemplate();
        $challengesSheet = $spreadsheet->getSheet(0);

        // Extract all headers from row 1
        $headers = [];
        for ($col = 'A'; $col <= 'S'; $col++) {
            $headers[] = $challengesSheet->getCell($col . '1')->getValue();
        }

        // Verify required columns for import (from ChallengesImport::assertChallengeRow)
        $requiredColumns = [
            'id', 'name', 'short_description', 'description', 'max_points', 'starts_at', 'expires_at',
            'skill_analytical', 'skill_strategicplanning', 'skill_adaptability', 'skill_premierleagueknowledge',
            'skill_riskmanagement', 'skill_decisionmakingunderpressure', 'skill_financialmanagement',
            'skill_longtermvision',
        ];

        foreach ($requiredColumns as $requiredColumn) {
            $this->assertContains(
                $requiredColumn,
                $headers,
                sprintf('Required import column "%s" must be present in template', $requiredColumn)
            );
        }
    }

    public function testQuestionsHeadersMatchImportRequirements(): void
    {
        $spreadsheet = $this->exporter->exportTemplate();
        $questionsSheet = $spreadsheet->getSheet(1);

        // Extract all headers from row 1
        $headers = [];
        for ($col = 'A'; $col <= 'I'; $col++) {
            $headers[] = $questionsSheet->getCell($col . '1')->getValue();
        }

        // Verify required columns for import (from ChallengesImport::assertQuestionRow)
        $requiredColumns = [
            'challenge_id', 'text', 'type', 'image', 'numeric_type_min', 'numeric_type_max',
            'choices', 'choices_min_selections', 'choices_max_selections',
        ];

        foreach ($requiredColumns as $requiredColumn) {
            $this->assertContains(
                $requiredColumn,
                $headers,
                sprintf('Required import column "%s" must be present in template', $requiredColumn)
            );
        }
    }

    public function testHeadersAreNormalizedFormat(): void
    {
        $spreadsheet = $this->exporter->exportTemplate();
        $challengesSheet = $spreadsheet->getSheet(0);
        $questionsSheet = $spreadsheet->getSheet(1);

        // All headers should be lowercase with underscores (snake_case)
        // This matches the normalizeHeader method in ChallengesImport
        for ($col = 'A'; $col <= 'R'; $col++) {
            $header = $challengesSheet->getCell($col . '1')->getValue();
            if ($header !== null && is_string($header)) {
                $this->assertMatchesRegularExpression(
                    '/^[a-z0-9_]+$/',
                    $header,
                    sprintf('Challenge header "%s" must be normalized (lowercase, underscores)', $header)
                );
            }
        }

        for ($col = 'A'; $col <= 'I'; $col++) {
            $header = $questionsSheet->getCell($col . '1')->getValue();
            if ($header !== null && is_string($header)) {
                $this->assertMatchesRegularExpression(
                    '/^[a-z0-9_]+$/',
                    $header,
                    sprintf('Question header "%s" must be normalized (lowercase, underscores)', $header)
                );
            }
        }
    }
}
