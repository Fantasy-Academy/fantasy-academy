<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\Subscription;

use FantasyAcademy\API\Api\Subscription\CheckoutSessionResponse;
use FantasyAcademy\API\Message\Subscription\CreateCheckoutSession;
use FantasyAcademy\API\Repository\UserRepository;
use FantasyAcademy\API\Services\Stripe\StripeClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly final class CreateCheckoutSessionHandler
{
    private const string LOOKUP_KEY_MONTHLY = 'fantasy_academy_monthly';
    private const string LOOKUP_KEY_YEARLY = 'fantasy_academy_yearly';

    public function __construct(
        private UserRepository $userRepository,
        private StripeClientInterface $stripeClient,
        #[Autowire(env: 'FRONTEND_URI')]
        private string $frontendUri,
    ) {
    }

    public function __invoke(CreateCheckoutSession $message): CheckoutSessionResponse
    {
        $user = $this->userRepository->getById($message->userId());
        $userId = $user->id->toString();

        // Create Stripe customer if not exists
        $customerId = $user->stripeCustomerId;
        if ($customerId === null) {
            $customer = $this->stripeClient->createCustomer($user->email, $user->name, $userId);
            $user->updateStripeCustomerId($customer->customerId);
            $customerId = $customer->customerId;
        }

        // Get price by lookup key
        $lookupKey = $message->plan === 'yearly' ? self::LOOKUP_KEY_YEARLY : self::LOOKUP_KEY_MONTHLY;
        $prices = $this->stripeClient->getPricesByLookupKeys($lookupKey);

        if ($prices === []) {
            throw new \RuntimeException("Price with lookup key '{$lookupKey}' not found in Stripe. Run app:stripe:init command.");
        }

        $priceId = $prices[0]->priceId;

        // Build URLs
        $successUrl = $message->successUrl ?? $this->frontendUri . '/subscription/success?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = $message->cancelUrl ?? $this->frontendUri . '/subscription/cancel';

        // Create checkout session with user_id in metadata
        $session = $this->stripeClient->createCheckoutSession(
            $customerId,
            $priceId,
            $successUrl,
            $cancelUrl,
            $userId,
        );

        return new CheckoutSessionResponse(
            checkoutUrl: $session->url,
            sessionId: $session->sessionId,
        );
    }
}
