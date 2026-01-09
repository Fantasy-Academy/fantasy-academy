<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\Subscription;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use FantasyAcademy\API\Api\Subscription\CheckoutSessionResponse;
use FantasyAcademy\API\Message\UserAware;
use FantasyAcademy\API\Message\WithUserId;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Create checkout session',
)]
#[Post(
    uriTemplate: '/subscription/checkout',
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
    output: CheckoutSessionResponse::class,
    messenger: 'input',
)]
readonly final class CreateCheckoutSession implements UserAware
{
    use WithUserId;

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['monthly', 'yearly'])]
        public string $plan,
        #[Assert\Url]
        public ?string $successUrl = null,
        #[Assert\Url]
        public ?string $cancelUrl = null,
        private ?Uuid $userId = null,
    ) {
    }
}
