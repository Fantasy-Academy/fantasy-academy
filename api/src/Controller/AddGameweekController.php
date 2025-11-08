<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Controller;

use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\FormData\GameweekFormData;
use FantasyAcademy\API\FormType\GameweekFormType;
use FantasyAcademy\API\Message\Gameweek\AddGameweek;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Throwable;

final class AddGameweekController extends AbstractController
{
    public function __construct(
        readonly private MessageBusInterface $messageBus,
        readonly private LoggerInterface $logger,
    ) {
    }

    #[Route(path: '/admin/gameweeks/add', name: 'add_gameweek')]
    public function __invoke(Request $request, #[CurrentUser] User $user): Response
    {
        $data = new GameweekFormData();
        $form = $this->createForm(GameweekFormType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                assert($data->number !== null);
                assert($data->startsAt !== null);
                assert($data->endsAt !== null);

                $this->messageBus->dispatch(new AddGameweek(
                    season: $data->season,
                    number: $data->number,
                    title: $data->title,
                    description: $data->description,
                    startsAt: $data->startsAt,
                    endsAt: $data->endsAt,
                ));

                $this->addFlash('success', 'Gameweek created successfully.');

                return $this->redirectToRoute('manage_gameweeks');
            } catch (Throwable $exception) {
                $this->addFlash('error', 'Failed to create gameweek: ' . $exception->getMessage());

                $this->logger->error('Gameweek creation failed', [
                    'exception' => $exception,
                    'formData' => [
                        'season' => $data->season,
                        'number' => $data->number,
                    ],
                ]);
            }
        }

        return $this->render('gameweek_form.html.twig', [
            'form' => $form,
            'isEdit' => false,
        ]);
    }
}
