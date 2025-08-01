<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message;

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
readonly final class RequestForgottenPasswordResetCode
{
    public function __construct(
        public string $email,
    ) {}
}
