<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Events;

readonly final class UserRegistered
{
    public function __construct(
        public string $email,
    ) {
    }
}
