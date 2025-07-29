<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use FantasyAcademy\API\Api\Processor\AnswerQuestionProcessor;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    shortName: 'Answer question',
)]
#[Put(
    uriTemplate: '/questions/answer',
    status: 204,
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
    input: self::class,
    output: false,
    messenger: 'input',
    read: false,
    processor: AnswerQuestionProcessor::class,
)]
readonly final class AnswerQuestionRequest
{
    /**
     * @param null|array<Uuid> $selectedChoiceIds
     * @param null|array<Uuid> $orderedChoiceIds
     */
    public function __construct(
        public Uuid $questionId,
        public null|string $textAnswer = null,
        public null|float $numericAnswer = null,
        public null|Uuid $selectedChoiceId = null,
        public null|array $selectedChoiceIds = [],
        public null|array $orderedChoiceIds = [],
    ) {}
}
