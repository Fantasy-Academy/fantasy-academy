<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Controller;

use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Exceptions\GameweekNotFound;
use FantasyAcademy\API\FormData\GameweekFormData;
use FantasyAcademy\API\FormType\GameweekFormType;
use FantasyAcademy\API\Message\Gameweek\EditGameweek;
use FantasyAcademy\API\Query\GameweekQuery;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;
use Throwable;

final class EditGameweekController extends AbstractController
{
    public function __construct(
        readonly private GameweekQuery $gameweekQuery,
        readonly private MessageBusInterface $messageBus,
        readonly private LoggerInterface $logger,
    ) {
    }

    #[Route(path: '/admin/gameweeks/{id}/edit', name: 'edit_gameweek')]
    public function __invoke(string $id, Request $request, #[CurrentUser] User $user): Response
    {
        try {
            $gameweekId = Uuid::fromString($id);
            $gameweek = $this->gameweekQuery->getById($id);

            if ($gameweek === null) {
                throw new GameweekNotFound();
            }

            $data = new GameweekFormData();
            $data->season = $gameweek->season;
            $data->number = $gameweek->number;
            $data->title = $gameweek->title;
            $data->description = $gameweek->description;
            $data->startsAt = $gameweek->startsAt;
            $data->endsAt = $gameweek->endsAt;

            $form = $this->createForm(GameweekFormType::class, $data);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    // @phpstan-ignore function.alreadyNarrowedType, notIdentical.alwaysTrue
                    assert($data->season !== null);
                    // @phpstan-ignore function.alreadyNarrowedType, notIdentical.alwaysTrue
                    assert($data->number !== null);
                    // @phpstan-ignore function.alreadyNarrowedType, notIdentical.alwaysTrue
                    assert($data->startsAt !== null);
                    // @phpstan-ignore function.alreadyNarrowedType, notIdentical.alwaysTrue
                    assert($data->endsAt !== null);

                    $this->messageBus->dispatch(new EditGameweek(
                        id: $gameweekId,
                        season: $data->season,
                        number: $data->number,
                        title: $data->title,
                        description: $data->description,
                        startsAt: $data->startsAt,
                        endsAt: $data->endsAt,
                    ));

                    $this->addFlash('success', 'Gameweek updated successfully.');

                    return $this->redirectToRoute('manage_gameweeks');
                } catch (Throwable $exception) {
                    $this->addFlash('error', 'Failed to update gameweek: ' . $exception->getMessage());

                    $this->logger->error('Gameweek update failed', [
                        'exception' => $exception,
                        'gameweekId' => $id,
                        'formData' => [
                            'season' => $data->season,
                            'number' => $data->number,
                        ],
                    ]);
                }
            }

            return $this->render('gameweek_form.html.twig', [
                'form' => $form,
                'isEdit' => true,
                'gameweekId' => $id,
            ]);
        } catch (GameweekNotFound) {
            $this->addFlash('error', 'Gameweek not found.');

            return $this->redirectToRoute('manage_gameweeks');
        }
    }
}
