<?php
declare(strict_types=1);

namespace FantasyAcademy\API\Controller;

use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\FormData\ExcelImportFormData;
use FantasyAcademy\API\FormType\ExcelImportFormType;
use FantasyAcademy\API\Services\Import\ChallengesImport;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Throwable;

final class ImportChallengesController extends AbstractController
{
    public function __construct(
        readonly private ChallengesImport $challengesImport,
        readonly Private LoggerInterface $logger,
    ) {
    }

    #[Route(path: '/admin/import-challenges', name: 'import_challenges')]
    public function __invoke(Request $request, #[CurrentUser] User $user): Response
    {
        $data = new ExcelImportFormData();
        $form = $this->createForm(ExcelImportFormType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            assert($data->file !== null);

            try {
                $this->challengesImport->importFile($data->file);

                $this->addFlash('success', 'Challenges imported successfully.');
            } catch (Throwable $exception) {
                $this->addFlash('error', $exception->getMessage());

                $this->logger->error('Challenge import failed', [
                    'exception' => $exception,
                ]);
            }

            return $this->redirectToRoute('import_challenges');
        }

        return $this->render('import_challenges.html.twig', [
            'form' => $form,
        ]);
    }
}
