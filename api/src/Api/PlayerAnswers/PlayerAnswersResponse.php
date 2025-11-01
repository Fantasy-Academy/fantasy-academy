<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\PlayerAnswers;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type PlayerChallengeAnswerRow array{
 *     challenge_id: string,
 *     challenge_name: string,
 *     challenge_evaluated_at: string,
 *     points: null|int,
 *     question_id: string,
 *     question_text: string,
 *     text_answer: null|string,
 *     numeric_answer: null|string,
 *     selected_choice_id: null|string,
 *     selected_choice_ids: null|string,
 *     ordered_choice_ids: null|string,
 *     gameweek: null|int,
 * }
 */
#[ApiResource(
    shortName: 'Player answers',
)]
#[Get(
    uriTemplate: '/players/{id}/answers',
    provider: PlayerAnswersProvider::class,
)]
readonly final class PlayerAnswersResponse
{
    /**
     * @param array<PlayerChallengeData> $challenges
     */
    public function __construct(
        public Uuid $id,
        public array $challenges,
    ) {
    }
}
