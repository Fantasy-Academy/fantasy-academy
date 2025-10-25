<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Controller;

use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Services\Export\ChallengeTemplateExport;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class ExportChallengeTemplateController extends AbstractController
{
    public function __construct(
        readonly private ChallengeTemplateExport $challengeTemplateExport,
    ) {
    }

    #[Route(path: '/admin/export-challenge-template', name: 'export_challenge_template')]
    public function __invoke(#[CurrentUser] User $user): Response
    {
        $spreadsheet = $this->challengeTemplateExport->exportTemplate();

        $response = new StreamedResponse(function () use ($spreadsheet): void {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="fa - challenge template.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
