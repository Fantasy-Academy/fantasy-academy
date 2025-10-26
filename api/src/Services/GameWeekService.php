<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services;

use DateTimeImmutable;

readonly final class GameWeekService
{
    /**
     * Calculate the cutoff time for the previous game week (last Monday 23:59:59).
     */
    public static function getLastMondayCutoff(DateTimeImmutable $now): DateTimeImmutable
    {
        $dayOfWeek = (int) $now->format('N'); // 1=Monday, 7=Sunday

        if ($dayOfWeek === 1) {
            // If today is Monday, get previous Monday
            return $now->modify('-1 week')->setTime(23, 59, 59);
        }

        // Get the most recent Monday
        $daysToSubtract = $dayOfWeek - 1;
        return $now->modify("-{$daysToSubtract} days")->setTime(23, 59, 59);
    }
}
