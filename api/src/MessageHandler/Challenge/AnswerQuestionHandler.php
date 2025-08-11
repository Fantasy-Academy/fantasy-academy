<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\Challenge;

use FantasyAcademy\API\Entity\PlayerChallengeAnswer;
use FantasyAcademy\API\Exceptions\ChallengeExpired;
use FantasyAcademy\API\Exceptions\NotEnoughChoices;
use FantasyAcademy\API\Exceptions\TooManyChoices;
use FantasyAcademy\API\Exceptions\UserNotFound;
use FantasyAcademy\API\Message\Challenge\AnswerQuestion;
use FantasyAcademy\API\Repository\PlayerChallengeAnswerRepository;
use FantasyAcademy\API\Repository\QuestionRepository;
use FantasyAcademy\API\Repository\UserRepository;
use FantasyAcademy\API\Services\ProvideIdentity;
use Psr\Clock\ClockInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly final class AnswerQuestionHandler
{
    public function __construct(
        private QuestionRepository $questionRepository,
        private UserRepository $userRepository,
        private PlayerChallengeAnswerRepository $playerChallengeAnswerRepository,
        private ProvideIdentity $provideIdentity,
        private ClockInterface $clock,
    ) {
    }

    /**
     * @throws UserNotFound
     * @throws ChallengeExpired
     * @throws TooManyChoices
     * @throws NotEnoughChoices
     */
    public function __invoke(AnswerQuestion $message): void
    {
        $user = $this->userRepository->getById($message->userId());
        $question = $this->questionRepository->get($message->questionId);
        $challenge = $question->challenge;
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

        $playerChallengeAnswer->answerQuestion(
            $this->clock->now(),
            $question,
            textAnswer: $message->textAnswer,
            numericAnswer: $message->numericAnswer,
            selectedChoiceId: $message->selectedChoiceId,
            selectedChoiceIds: $message->selectedChoiceIds,
            orderedChoiceIds: $message->orderedChoiceIds,
        );

        $this->playerChallengeAnswerRepository->save($playerChallengeAnswer);
    }
}
