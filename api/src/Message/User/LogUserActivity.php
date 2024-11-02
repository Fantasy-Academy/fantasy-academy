<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\User;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

readonly final class LogUserActivity
{
    public function __construct(
        public UuidInterface $userId,
        public DateTimeImmutable $time,
    ) {
    }
}
