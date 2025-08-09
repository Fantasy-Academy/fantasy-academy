<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\User;

use FantasyAcademy\API\Events\UserRegistered;
use FantasyAcademy\API\Exceptions\UserNotFound;
use FantasyAcademy\API\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly final class SendEmailWhenUserRegistered
{
    public function __construct(
        private UserRepository $userRepository,
        private MailerInterface $mailer,
    ) {
    }

    /**
     * @throws UserNotFound
     */
    public function __invoke(UserRegistered $event): void
    {
        $user = $this->userRepository->getById($event->userId);

        $email = new TemplatedEmail()
            ->to($user->email)
            ->subject('Welcome to the Fantasy Academy')
            ->htmlTemplate('emails/welcome_registered_user.html.twig')
            ->context([
                'userName' => $user->name,
            ]);

        $this->mailer->send($email);
    }
}
