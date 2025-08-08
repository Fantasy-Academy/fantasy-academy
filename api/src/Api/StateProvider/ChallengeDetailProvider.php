<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\DBAL\Connection;
use FantasyAcademy\API\Api\Response\ChallengeDetailResponse;
use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Exceptions\ChallengeNotFound;
use FantasyAcademy\API\Value\Question;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type ChallengeDetailResponseRow from ChallengeDetailResponse
 * @phpstan-import-type QuestionRow from Question
 *
 * @implements ProviderInterface<ChallengeDetailResponse>
 */
readonly final class ChallengeDetailProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private Connection $database,
        private ClockInterface $clock,
    ) {}

    /**
     * @param array{id: Uuid} $uriVariables
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ChallengeDetailResponse
    {
        /** @var null|User $user */
        $user = $this->security->getUser();

        $challengeId = $uriVariables['id'];

        return $this->getChallengeDetail($challengeId, $user?->id);
    }

    private function getChallengeDetail(Uuid $challengeId, null|Uuid $userId): ChallengeDetailResponse
    {
        $query = <<<SQL
SELECT challenge.*, player_challenge_answer.answered_at
FROM challenge
LEFT JOIN player_challenge_answer ON challenge.id = player_challenge_answer.challenge_id AND player_challenge_answer.user_id = :userId
WHERE challenge.id = :challengeId
SQL;

        /** @var false|ChallengeDetailResponseRow $rows */
        $row = $this->database
            ->executeQuery($query, [
                'challengeId' => $challengeId->toString(),
                'userId' => $userId?->toString(),
            ])
            ->fetchAssociative();

        if ($row === false) {
            throw new ChallengeNotFound();
        }

        return ChallengeDetailResponse::fromDatabaseRow(
            $row,
            $this->clock->now(),
            $this->getQuestions($challengeId, $userId),
        );
    }

    /**
     * @return array<QuestionRow>
     */
    private function getQuestions(Uuid $challengeId, null|Uuid $userId): array
    {
        $query = <<<SQL
SELECT question.*
FROM question
LEFT JOIN player_challenge_answer ON question.challenge_id = player_challenge_answer.challenge_id AND player_challenge_answer.user_id = :userId
LEFT JOIN player_answered_question ON player_answered_question.challenge_answer_id = player_challenge_answer.id AND player_answered_question.question_id = question.id
WHERE question.challenge_id = :challengeId
SQL;

        /** @var false|ChallengeDetailResponseRow $rows */
        $row = $this->database
            ->executeQuery($query, [
                'challengeId' => $challengeId->toString(),
                'userId' => $userId?->toString(),
            ])
            ->fetchAssociative();

        return [];
    }
}
