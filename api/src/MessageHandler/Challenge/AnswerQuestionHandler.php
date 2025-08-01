<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\Challenge;

use FantasyAcademy\API\Message\Challenge\AnswerQuestion;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly final class AnswerQuestionHandler
{
    public function __invoke(AnswerQuestion $message): void
    {
        $userId = $message->userId();

        return;
    }
}
