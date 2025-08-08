<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\Challenge;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use FantasyAcademy\API\Message\UserAware;
use FantasyAcademy\API\Message\WithUserId;
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
)]
readonly final class AnswerQuestion implements UserAware
{
    use WithUserId;

    /**
     * @param null|array<Uuid> $selectedChoiceIds
     * @param null|array<Uuid> $orderedChoiceIds
     */
    public function __construct(
        public Uuid $questionId,
        public null|string $textAnswer = null,
        public null|float $numericAnswer = null,
        public null|Uuid $selectedChoiceId = null,
        public null|array $selectedChoiceIds = null,
        public null|array $orderedChoiceIds = null,
        private null|Uuid $userId = null,
    ) {}
}
