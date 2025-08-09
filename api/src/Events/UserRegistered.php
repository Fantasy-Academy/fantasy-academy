<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Events;

use Symfony\Component\Uid\Uuid;

readonly final class UserRegistered
{
    public function __construct(
        public Uuid $userId,
    ) {
    }
}
