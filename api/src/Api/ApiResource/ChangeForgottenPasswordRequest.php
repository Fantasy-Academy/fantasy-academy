<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;

#[ApiResource(
    shortName: 'Forgotten password change',
)]
#[Post(
    uriTemplate: '/forgotten-password/change',
    status: 204,
    input: self::class,
    output: false,
    messenger: 'input',
)]
readonly final class ChangeForgottenPasswordRequest
{
    public function __construct(
        public string $code,
        public string $newPassword,
    ) {}
}
