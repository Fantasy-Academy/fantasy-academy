<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\User;

readonly final class AddUser
{
    public function __construct(
        public string $email,
        public string $plainTextPassword,
        public string $name,
        public string $role,
    ) {
    }
}
