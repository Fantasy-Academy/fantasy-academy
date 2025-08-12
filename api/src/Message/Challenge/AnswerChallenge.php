<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\Challenge;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use FantasyAcademy\API\Message\UserAware;
use FantasyAcademy\API\Message\WithUserId;
use FantasyAcademy\API\Value\QuestionAnswer;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    shortName: 'Answer challenge',
)]
#[Put(
    uriTemplate: '/challenges/answer',
    status: 204,
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
    input: self::class,
    output: false,
    messenger: 'input',
    read: false,
)]
readonly final class AnswerChallenge implements UserAware
{
    use WithUserId;

    /**
     * @param array<QuestionAnswer> $answers
     */
    public function __construct(
        public Uuid $challengeId,
        public array $answers,

        #[ApiProperty(readable: false, writable: false)]
        private null|Uuid $userId = null,
    ) {}
}
