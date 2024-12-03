<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Entity;

use DateTimeImmutable;

readonly final class UserChallengeAnswer
{
    public Challenge $challenge;
    public User $user;
    public null|int $score;
    public DateTimeImmutable $answeredAt;

    // 1 ... x odpovedi
}
