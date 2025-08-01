<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\User;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

readonly final class LogUserActivity
{
    public function __construct(
        public Uuid $userId,
        public DateTimeImmutable $time,
    ) {
    }
}
