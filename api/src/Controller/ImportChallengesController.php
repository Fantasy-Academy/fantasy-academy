<?php
declare(strict_types=1);

namespace FantasyAcademy\API\Controller;

use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\FormData\ExcelImportFormData;
use FantasyAcademy\API\FormType\ExcelImportFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class ImportChallengesController extends AbstractController
{
    #[Route(path: '/admin/import-challenges', name: 'import_challenges')]
    public function __invoke(Request $request, #[CurrentUser] User $user): Response
    {
        $data = new ExcelImportFormData();
        $form = $this->createForm(ExcelImportFormType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        }

        return $this->render('import_challenges.html.twig', [
            'form' => $form,
        ]);
    }
}
