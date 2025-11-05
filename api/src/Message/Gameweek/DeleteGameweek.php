<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\Gameweek;

use Symfony\Component\Uid\Uuid;

readonly final class DeleteGameweek
{
    public function __construct(
        public Uuid $id,
    ) {
    }
}
