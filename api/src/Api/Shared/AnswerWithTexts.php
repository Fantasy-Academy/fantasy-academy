<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Shared;

use Symfony\Component\Uid\Uuid;

readonly final class AnswerWithTexts
{
    /**
     * @param null|array<Uuid> $selectedChoiceIds
     * @param null|array<string> $selectedChoiceTexts
     * @param null|array<Uuid> $orderedChoiceIds
     * @param null|array<string> $orderedChoiceTexts
     */
    public function __construct(
        public null|string $textAnswer = null,
        public null|float $numericAnswer = null,
        public null|Uuid $selectedChoiceId = null,
        public null|string $selectedChoiceText = null,
        public null|array $selectedChoiceIds = null,
        public null|array $selectedChoiceTexts = null,
        public null|array $orderedChoiceIds = null,
        public null|array $orderedChoiceTexts = null,
    ) {}
}
