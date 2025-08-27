<?php
declare(strict_types=1);

namespace FantasyAcademy\API\Controller;

use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\FormData\ExcelImportFormData;
use FantasyAcademy\API\FormType\ExcelImportFormType;
use FantasyAcademy\API\Services\Import\ChallengesImport;
use FantasyAcademy\API\Services\Import\ChallengesResultsImport;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Throwable;

final class ImportResultsController extends AbstractController
{
    public function __construct(
        readonly private ChallengesResultsImport $challengesResultsImport,
        readonly Private LoggerInterface $logger,
    ) {
    }

    #[Route(path: '/admin/import-results', name: 'import_results')]
    public function __invoke(Request $request, #[CurrentUser] User $user): Response
    {
        $data = new ExcelImportFormData();
        $form = $this->createForm(ExcelImportFormType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            assert($data->file !== null);

            try {
                $this->challengesResultsImport->importFile($data->file);

                $this->addFlash('success', 'Results imported successfully.');
            } catch (Throwable $exception) {
                $this->addFlash('error', $exception->getMessage());

                $this->logger->error('Challenge import failed', [
                    'exception' => $exception,
                ]);
            }

            return $this->redirectToRoute('import_results');
        }

        return $this->render('import_results.html.twig', [
            'form' => $form,
        ]);
    }
}
