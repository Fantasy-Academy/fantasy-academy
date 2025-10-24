<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

use Symfony\Component\Uid\Uuid;

readonly final class Choice
{
    public function __construct(
        public Uuid $id,
        public string $text,
        public null|string $description = null,
        public null|string $image = null,
    ) {}
}
