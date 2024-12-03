<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Entity;

use DateTimeImmutable;

readonly final class GameWeek
{
    public SeasonPhase $phase;
    public DateTimeImmutable $startDate;
    public DateTimeImmutable $endDate;
    public DateTimeImmutable $deadline;
    public int $number;
}
