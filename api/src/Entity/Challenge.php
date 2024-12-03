<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Entity;

use DateTimeImmutable;

readonly final class Challenge
{
    public GameWeek $startWeek;
    public GameWeek $endWeek;
    public null|string $image;
    public string $title;
    public string $description;
    public string $question;
    public null|string $hint;
    public null|string $hintImage;
    public ChallengeType $type;
    public ChallengeScoring $scoring;

    // mam 10 bodu za vyzvu
    // rikam ze 40% jde do skillu A
    // rikam ze 30% jde do skillu B
    // rikam ze 30% jde do skillu C
    public array $skills;
}
