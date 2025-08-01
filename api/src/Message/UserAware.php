<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message;

use Symfony\Component\Uid\Uuid;

interface UserAware
{
    public function withUserId(Uuid $userId): static;

    public function userId(): Uuid;
}
