<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services;

use Symfony\Component\Uid\Uuid;

interface ProvideIdentity
{
    public function next(): Uuid;
}
