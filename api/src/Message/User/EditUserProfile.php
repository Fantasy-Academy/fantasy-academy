<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\User;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use FantasyAcademy\API\Message\UserAware;
use FantasyAcademy\API\Message\WithUserId;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    shortName: 'Edit user profile',
)]
#[Put(
    uriTemplate: '/me/edit-profile',
    status: 204,
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
    input: self::class,
    output: false,
    messenger: 'input',
    read: false,
)]
readonly final class EditUserProfile implements UserAware
{
    use WithUserId;

    public function __construct(
        public string $name,
        private null|Uuid $userId,
    ) {
    }
}
