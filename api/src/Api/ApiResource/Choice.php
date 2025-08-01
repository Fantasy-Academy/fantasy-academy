<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use Symfony\Component\Uid\Uuid;

readonly final class Choice
{
    public function __construct(
        public Uuid $id,
        public string $text,
        public null|string $description,
        public null|string $image,
    ) {}
}
