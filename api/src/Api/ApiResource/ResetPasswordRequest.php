<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;

#[ApiResource(
    shortName: 'Forgotten password',
)]
#[Post(
    uriTemplate: '/forgotten-password',
    status: 204,
    input: self::class,
    output: false,
    messenger: 'input',
)]
readonly final class ResetPasswordRequest
{
    public function __construct(
        public string $email,
    ) {}
}
