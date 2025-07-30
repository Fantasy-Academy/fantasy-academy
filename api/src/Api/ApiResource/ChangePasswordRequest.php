<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;

#[ApiResource(
    shortName: 'Change user password',
)]
#[Put(
    uriTemplate: '/me/change-password',
    status: 204,
    input: self::class,
    output: false,
    messenger: 'input',
)]
readonly final class ChangePasswordRequest
{
    public function __construct(
        public string $newPassword,
    ) {}
}
