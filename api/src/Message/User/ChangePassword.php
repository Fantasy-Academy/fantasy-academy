<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\User;

readonly final class ChangePassword
{
    public function __construct(
        public string $userEmail,
        public string $newPlainTextPassword,
    ) {
    }
}
