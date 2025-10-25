<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Controller;

use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\FormData\ExportAnswersFormData;
use FantasyAcademy\API\FormType\ExportAnswersFormType;
use FantasyAcademy\API\Query\ChallengeQuery;
use FantasyAcademy\API\Services\Export\PlayersAnswersExport;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Throwable;

final class ExportAnswersController extends AbstractController
{
    public function __construct(
        readonly private PlayersAnswersExport $playersAnswersExport,
        readonly private LoggerInterface $logger,
        readonly private ChallengeQuery $challengeQuery,
    ) {
    }

    #[Route(path: '/admin/export-answers', name: 'export_answers')]
    public function __invoke(Request $request, #[CurrentUser] User $user): Response
    {
        $challenges = $this->challengeQuery->getAll();

        $data = new ExportAnswersFormData();
        $form = $this->createForm(ExportAnswersFormType::class, $data, [
            'challenges' => $challenges,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            assert(!empty($data->challengeIds));

            try {
                $spreadsheet = $this->playersAnswersExport->exportAnswers($data->challengeIds);
                
                $response = new StreamedResponse(function () use ($spreadsheet): void {
                    $writer = new Xlsx($spreadsheet);
                    $writer->save('php://output');
                });
                
                $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                $response->headers->set('Content-Disposition', 'attachment; filename="fa - players answers.xlsx"');
                $response->headers->set('Cache-Control', 'max-age=0');
                
                return $response;
            } catch (Throwable $exception) {
                $this->addFlash('error', 'Export failed: ' . $exception->getMessage());

                $this->logger->error('Answers export failed', [
                    'exception' => $exception,
                    'challengeIds' => $data->challengeIds,
                ]);
            }

            return $this->redirectToRoute('export_answers');
        }

        return $this->render('export_answers.html.twig', [
            'form' => $form,
            'challenges' => $challenges,
        ]);
    }
}