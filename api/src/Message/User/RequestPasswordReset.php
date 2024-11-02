<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\User;

readonly final class RequestPasswordReset
{
    public function __construct(
        public string $email,
    ) {
    }
}
