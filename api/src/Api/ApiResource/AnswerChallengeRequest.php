<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use FantasyAcademy\API\Api\Processor\AnswerChallengeProcessor;

#[ApiResource(
    shortName: 'Answer challenge',
)]
#[Post(
    uriTemplate: '/challenges/{id}/answer',
    status: 204,
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
    input: self::class,
    output: false,
    messenger: 'input',
    processor: AnswerChallengeProcessor::class,
)]
readonly final class AnswerChallengeRequest
{
    public function __construct(
        public string $answer,
    ) {}
}
