<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

readonly class ProvideIdentity
{
    public function next(): UuidInterface
    {
        return Uuid::uuid7();
    }
}
