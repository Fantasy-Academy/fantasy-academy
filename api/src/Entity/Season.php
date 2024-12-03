<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Entity;

use DateTimeImmutable;

readonly final class Season
{
    public DateTimeImmutable $startDate;
    public DateTimeImmutable $endDate;
    public string $name;
}
