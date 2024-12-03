<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Entity;

use DateTimeImmutable;

readonly final class UserAchievment
{
    public Achievment $achievment;
    public User $user;
    public DateTimeImmutable $achievedAt;
}
