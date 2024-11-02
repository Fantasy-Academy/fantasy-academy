<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\User;

readonly final class ResetPassword
{
    public function __construct(
        public string $token,
        public string $newPlainTextPassword,
    ) {
    }
}
