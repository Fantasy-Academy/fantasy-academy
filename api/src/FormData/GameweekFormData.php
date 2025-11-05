<?php

declare(strict_types=1);

namespace FantasyAcademy\API\FormData;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

final class GameweekFormData
{
    #[NotBlank]
    #[Positive]
    public null|int $season = null;

    #[NotBlank]
    #[Positive]
    public null|int $number = null;

    public null|string $title = null;

    public null|string $description = null;

    #[NotBlank]
    public null|DateTimeImmutable $startsAt = null;

    #[NotBlank]
    #[GreaterThan(propertyPath: 'startsAt', message: 'End date must be after start date.')]
    public null|DateTimeImmutable $endsAt = null;
}
