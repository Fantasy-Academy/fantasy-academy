<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\Subscription;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use FantasyAcademy\API\Api\Subscription\PortalSessionResponse;
use FantasyAcademy\API\Message\UserAware;
use FantasyAcademy\API\Message\WithUserId;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Create billing portal session',
)]
#[Post(
    uriTemplate: '/subscription/portal',
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
    output: PortalSessionResponse::class,
    messenger: 'input',
)]
readonly final class CreatePortalSession implements UserAware
{
    use WithUserId;

    public function __construct(
        #[Assert\Url]
        public ?string $returnUrl = null,
        private ?Uuid $userId = null,
    ) {
    }
}
