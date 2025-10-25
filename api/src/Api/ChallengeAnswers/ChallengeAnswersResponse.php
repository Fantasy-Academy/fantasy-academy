<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ChallengeAnswers;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type PlayerAnswerRow array{
 *     user_id: string,
 *     user_name: string,
 *     points: int,
 *     question_id: string,
 *     question_text: string,
 *     text_answer: null|string,
 *     numeric_answer: null|string,
 *     selected_choice_id: null|string,
 *     selected_choice_ids: null|string,
 *     ordered_choice_ids: null|string,
 * }
 */
#[ApiResource(
    shortName: 'Challenge answers',
)]
#[Get(
    uriTemplate: '/challenges/{id}/answers',
    provider: ChallengeAnswersProvider::class,
)]
readonly final class ChallengeAnswersResponse
{
    /**
     * @param array<PlayerAnswerData> $players
     */
    public function __construct(
        public Uuid $id,
        public array $players,
    ) {
    }
}
