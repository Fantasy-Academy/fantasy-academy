<?php
declare(strict_types=1);

namespace FantasyAcademy\API\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomepageController extends AbstractController
{
    #[Route(path: '/', name: 'homepage')]
    public function __invoke(): Response
    {
        return $this->render('homepage.html.twig');
    }
}
