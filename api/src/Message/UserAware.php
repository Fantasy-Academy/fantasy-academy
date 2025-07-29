<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message;

use Ramsey\Uuid\UuidInterface;

interface UserAware
{
    public function withUserId(UuidInterface $userId): static;
}
