<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Export;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

readonly final class ChallengeTemplateExport
{
    public function exportTemplate(): Spreadsheet
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
            'skill_analytical',
            'skill_strategicplanning',
            'skill_adaptability',
            'skill_premierleagueknowledge',
            'skill_riskmanagement',
            'skill_decisionmakingunderpressure',
            'skill_financialmanagement',
            'skill_longtermvision',
        ]);

        // Second sheet: Questions
        $questionsSheet = $spreadsheet->createSheet();
        $questionsSheet->setTitle('Questions');
        $questionsSheet->fromArray([
            'challenge_id',
            'text',
            'type',
            'image',
            'numeric_type_min',
            'numeric_type_max',
            'choices',
            'choices_min_selections',
            'choices_max_selections',
        ]);

        return $spreadsheet;
    }
}
