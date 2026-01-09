<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\Subscription;

use FantasyAcademy\API\Api\Subscription\PortalSessionResponse;
use FantasyAcademy\API\Message\Subscription\CreatePortalSession;
use FantasyAcademy\API\Repository\UserRepository;
use FantasyAcademy\API\Services\Stripe\StripeClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly final class CreatePortalSessionHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private StripeClientInterface $stripeClient,
        #[Autowire(env: 'FRONTEND_URI')]
        private string $frontendUri,
    ) {
    }

    public function __invoke(CreatePortalSession $message): PortalSessionResponse
    {
        $user = $this->userRepository->getById($message->userId());

        if ($user->stripeCustomerId === null) {
            throw new BadRequestHttpException('User does not have a Stripe customer account');
        }

        $returnUrl = $message->returnUrl ?? $this->frontendUri . '/account';

        $session = $this->stripeClient->createPortalSession(
            $user->stripeCustomerId,
            $returnUrl,
        );

        return new PortalSessionResponse(
            portalUrl: $session->url,
        );
    }
}
