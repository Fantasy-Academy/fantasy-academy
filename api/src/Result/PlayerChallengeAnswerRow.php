<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Result;

/**
 * @phpstan-type PlayerChallengeAnswerRowArray array{
 *     id: string,
 *     user_id: string,
 *     challenge_id: string,
 *     points: int|numeric-string,
 * }
 */
readonly final class PlayerChallengeAnswerRow
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $challengeId,
        public int $points,
    ) {
    }

    /**
     * @param PlayerChallengeAnswerRowArray $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            userId: $data['user_id'],
            challengeId: $data['challenge_id'],
            points: (int) $data['points'],
        );
    }
}
