<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\User;

readonly final class RegisterUser
{
    public function __construct(
        public string $email,
        public string $plainTextPassword,
        public null|string $name,
        /** @var array<string> */
        public array $roles,
    ) {
    }
}
