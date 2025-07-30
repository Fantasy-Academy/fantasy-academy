<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;

#[ApiResource(
    shortName: 'Edit user profile',
)]
#[Put(
    uriTemplate: '/me/edit-profile',
    status: 204,
    input: self::class,
    output: false,
    messenger: 'input',
)]
readonly final class EditProfileRequest
{
    public function __construct(
        public string $name,
    ) {}
}
