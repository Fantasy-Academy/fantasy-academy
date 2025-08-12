<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\User;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use FantasyAcademy\API\Message\UserAware;
use FantasyAcademy\API\Message\WithUserId;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    shortName: 'Change user password',
)]
#[Put(
    uriTemplate: '/me/change-password',
    status: 204,
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
    input: self::class,
    output: false,
    messenger: 'input',
    read: false,
)]
readonly final class ChangePassword implements UserAware
{
    use WithUserId;

    public function __construct(
        public string $newPassword,

        #[ApiProperty(readable: false, writable: false)]
        private null|Uuid $userId = null,
    ) {}
}
