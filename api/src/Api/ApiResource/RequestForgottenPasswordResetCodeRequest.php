<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;

#[ApiResource(
    shortName: 'Forgotten password - request reset code',
)]
#[Put(
    uriTemplate: '/forgotten-password/request-reset-code',
    status: 204,
    input: self::class,
    output: false,
    messenger: 'input',
)]
readonly final class RequestForgottenPasswordResetCodeRequest
{
    public function __construct(
        public string $email,
    ) {}
}
