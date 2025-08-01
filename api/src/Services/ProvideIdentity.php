<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services;

use Symfony\Component\Uid\Uuid;

readonly class ProvideIdentity
{
    public function next(): Uuid
    {
        return Uuid::v7();
    }
}
