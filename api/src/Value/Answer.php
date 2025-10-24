<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

use Symfony\Component\Uid\Uuid;

readonly final class Answer
{
    /**
     * @param null|array<Uuid> $selectedChoiceIds
     * @param null|array<Uuid> $orderedChoiceIds
     */
    public function __construct(
        public null|string $textAnswer = null,
        public null|float $numericAnswer = null,
        public null|Uuid $selectedChoiceId = null,
        public null|array $selectedChoiceIds = null,
        public null|array $orderedChoiceIds = null,
    ) {}
}
