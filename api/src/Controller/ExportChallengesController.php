<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Controller;

use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\FormData\ExportChallengesFormData;
use FantasyAcademy\API\FormType\ExportChallengesFormType;
use FantasyAcademy\API\Query\ChallengeQuery;
use FantasyAcademy\API\Services\Export\ChallengesExport;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Throwable;

final class ExportChallengesController extends AbstractController
{
    public function __construct(
        readonly private ChallengesExport $challengesExport,
        readonly private LoggerInterface $logger,
        readonly private ChallengeQuery $challengeQuery,
    ) {
    }

    #[Route(path: '/admin/export-challenges', name: 'export_challenges')]
    public function __invoke(Request $request, #[CurrentUser] User $user): Response
    {
        $challenges = $this->challengeQuery->getAll();

        $data = new ExportChallengesFormData();
        $form = $this->createForm(ExportChallengesFormType::class, $data, [
            'challenges' => $challenges,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            assert(!empty($data->challengeIds));

            try {
                $spreadsheet = $this->challengesExport->exportChallenges($data->challengeIds);

                $response = new StreamedResponse(function () use ($spreadsheet): void {
                    $writer = new Xlsx($spreadsheet);
                    $writer->save('php://output');
                });

                $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                $response->headers->set('Content-Disposition', 'attachment; filename="fa - challenges.xlsx"');
                $response->headers->set('Cache-Control', 'max-age=0');

                return $response;
            } catch (Throwable $exception) {
                $this->addFlash('error', 'Export failed: ' . $exception->getMessage());

                $this->logger->error('Challenges export failed', [
                    'exception' => $exception,
                    'challengeIds' => $data->challengeIds,
                ]);
            }

            return $this->redirectToRoute('export_challenges');
        }

        return $this->render('export_challenges.html.twig', [
            'form' => $form,
            'challenges' => $challenges,
        ]);
    }
}
