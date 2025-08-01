<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\User;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;

#[ApiResource(
    shortName: 'Forgotten password - reset',
)]
#[Put(
    uriTemplate: '/forgotten-password/reset',
    status: 204,
    input: self::class,
    output: false,
    messenger: 'input',
    read: false,
)]
readonly final class ResetPassword
{
    public function __construct(
        public string $code,
        public string $newPassword,
    ) {
    }
}
