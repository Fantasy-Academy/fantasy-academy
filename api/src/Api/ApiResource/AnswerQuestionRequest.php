<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use FantasyAcademy\API\Api\Processor\AnswerQuestionProcessor;
use Ramsey\Uuid\UuidInterface;

#[ApiResource(
    shortName: 'Answer question',
)]
#[Put(
    uriTemplate: '/questions/{id}/answer',
    status: 204,
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
    input: self::class,
    output: false,
    messenger: 'input',
    processor: AnswerQuestionProcessor::class,
)]
readonly final class AnswerQuestionRequest
{
    /**
     * @param null|array<string> $selectedChoiceIds
     * @param null|array<string> $orderedChoiceIds
     */
    public function __construct(
        public null|string $textAnswer = null,
        public null|float $numericAnswer = null,
        public null|UuidInterface $selectedChoiceId = null,
        public null|array $selectedChoiceIds = [],
        public null|array $orderedChoiceIds = [],
    ) {}
}
