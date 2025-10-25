<?php

declare(strict_types=1);

namespace FantasyAcademy\API\FormData;

use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ExportChallengesFormData
{
    /**
     * @var array<string>
     */
    #[NotBlank(message: 'Please select at least one challenge.')]
    #[Count(min: 1, minMessage: 'Please select at least one challenge.')]
    public array $challengeIds = [];
}