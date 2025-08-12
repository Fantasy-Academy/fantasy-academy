<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

use Symfony\Component\Uid\Uuid;

readonly final class QuestionAnswer
{
    public function __construct(
        public Uuid $questionId,
        public Answer $answer,
    ) {}
}
