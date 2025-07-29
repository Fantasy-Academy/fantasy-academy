<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use Ramsey\Uuid\UuidInterface;

readonly final class Choice
{
    public function __construct(
        public UuidInterface $id,
        public string $text,
        public null|string $description,
        public null|string $image,
    ) {}
}
