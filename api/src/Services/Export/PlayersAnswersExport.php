<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Export;

use FantasyAcademy\API\Query\PlayerChallengeAnswerQuery;
use FantasyAcademy\API\Query\PlayerQuestionAnswerQuery;
use Symfony\Component\Uid\Uuid;

readonly final class PlayersAnswersExport
{
    public function __construct(
        private PlayerChallengeAnswerQuery $playerChallengeAnswerQuery,
        private PlayerQuestionAnswerQuery $playerQuestionAnswerQuery,
    ) {
    }

    /**
     * @param array<string> $challengeIds
     */
    public function exportAnswers(array $challengeIds)
    {
        foreach ($challengeIds as $challengeId) {
            $this->playerChallengeAnswerQuery->getForChallenge(Uuid::fromString($challengeId));
            $this->playerQuestionAnswerQuery->getForChallenge(Uuid::fromString($challengeId));
        }
    }
}
