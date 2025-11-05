<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Controller;

use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Message\Gameweek\DeleteGameweek;
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

final class ManageGameweeksController extends AbstractController
{
    public function __construct(
        readonly private GameweekQuery $gameweekQuery,
        readonly private MessageBusInterface $messageBus,
        readonly private LoggerInterface $logger,
    ) {
    }

    #[Route(path: '/admin/gameweeks', name: 'manage_gameweeks', methods: ['GET'])]
    public function list(#[CurrentUser] User $user): Response
    {
        $gameweeks = $this->gameweekQuery->getAll();

        return $this->render('gameweeks_list.html.twig', [
            'gameweeks' => $gameweeks,
        ]);
    }

    #[Route(path: '/admin/gameweeks/{id}/delete', name: 'delete_gameweek', methods: ['POST'])]
    public function delete(string $id, Request $request, #[CurrentUser] User $user): Response
    {
        try {
            $gameweekId = Uuid::fromString($id);

            $this->messageBus->dispatch(new DeleteGameweek($gameweekId));

            $this->addFlash('success', 'Gameweek deleted successfully.');
        } catch (Throwable $exception) {
            $this->addFlash('error', 'Failed to delete gameweek: ' . $exception->getMessage());

            $this->logger->error('Gameweek deletion failed', [
                'exception' => $exception,
                'gameweekId' => $id,
            ]);
        }

        return $this->redirectToRoute('manage_gameweeks');
    }
}
