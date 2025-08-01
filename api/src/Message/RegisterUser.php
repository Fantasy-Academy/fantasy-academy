<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;

#[ApiResource(
    shortName: 'Registration',
)]
#[Post(
    uriTemplate: '/register',
    status: 204,
    input: self::class,
    output: false,
    messenger: 'input',
)]
readonly final class RegisterUser
{
    public function __construct(
        public string $email,
        public string $password,
        public null|string $name,
    ) {}
}
