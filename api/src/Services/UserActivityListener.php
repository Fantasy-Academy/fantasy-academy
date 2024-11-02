<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services;

use Psr\Clock\ClockInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Message\User\LogUserActivity;

#[AsEventListener]
readonly final class UserActivityListener
{
    public function __construct(
        private Security $security,
        private MessageBusInterface $messageBus,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if ($event->isMainRequest() === false) {
            // don't do anything if it's not the main request
            return;
        }

        /** @var null|User $user */
        $user = $this->security->getUser();

        if ($user !== null) {
            $this->messageBus->dispatch(
                new LogUserActivity(
                    $user->id,
                    $this->clock->now(),
                ),
            );
        }
    }
}
