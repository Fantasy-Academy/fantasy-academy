<?php
declare(strict_types=1);

namespace FantasyAcademy\API\Controller;

use FantasyAcademy\API\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class AdminController extends AbstractController
{
    #[Route(path: '/admin', name: 'admin')]
    public function __invoke(#[CurrentUser] User $user): Response
    {
        return $this->render('admin.html.twig');
    }
}
