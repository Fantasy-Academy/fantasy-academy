<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\Challenge;

use FantasyAcademy\API\Entity\PlayerChallengeAnswer;
use FantasyAcademy\API\Exceptions\ChallengeExpired;
use FantasyAcademy\API\Exceptions\ChallengeNotFound;
use FantasyAcademy\API\Exceptions\NotEnoughChoices;
use FantasyAcademy\API\Exceptions\TooManyChoices;
use FantasyAcademy\API\Exceptions\UserNotFound;
use FantasyAcademy\API\Message\Challenge\AnswerChallenge;
use FantasyAcademy\API\Repository\ChallengeRepository;
use FantasyAcademy\API\Repository\PlayerChallengeAnswerRepository;
use FantasyAcademy\API\Repository\QuestionRepository;
use FantasyAcademy\API\Repository\UserRepository;
use FantasyAcademy\API\Services\ProvideIdentity;
use Psr\Clock\ClockInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly final class AnswerChallengeHandler
{
    public function __construct(
        private QuestionRepository $questionRepository,
        private UserRepository $userRepository,
        private PlayerChallengeAnswerRepository $playerChallengeAnswerRepository,
        private ProvideIdentity $provideIdentity,
        private ClockInterface $clock,
        private ChallengeRepository $challengeRepository,
    ) {
    }

    /**
     * @throws ChallengeNotFound
     * @throws UserNotFound
     * @throws ChallengeExpired
     * @throws TooManyChoices
     * @throws NotEnoughChoices
     */
    public function __invoke(AnswerChallenge $message): void
    {
        $user = $this->userRepository->getById($message->userId());
        $challenge = $this->challengeRepository->get($message->challengeId);
        $playerChallengeAnswer = $this->playerChallengeAnswerRepository->find(
            userId: $user->id,
            challengeId: $challenge->id,
        );

        if ($playerChallengeAnswer === null) {
            $playerChallengeAnswer = new PlayerChallengeAnswer(
                id: $this->provideIdentity->next(),
                challenge: $challenge,
                user: $user
            );
        }

        foreach ($message->answers as $questionAnswer) {
            $question = $this->questionRepository->get($questionAnswer->questionId);

            $playerChallengeAnswer->answerQuestion(
                $this->clock->now(),
                $question,
                textAnswer: $questionAnswer->answer->textAnswer,
                numericAnswer: $questionAnswer->answer->numericAnswer,
                selectedChoiceId: $questionAnswer->answer->selectedChoiceId,
                selectedChoiceIds: $questionAnswer->answer->selectedChoiceIds,
                orderedChoiceIds: $questionAnswer->answer->orderedChoiceIds,
            );

        }

        $this->playerChallengeAnswerRepository->save($playerChallengeAnswer);
    }
}
