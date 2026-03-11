<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\PlayerActivity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type PlayerActivityRow array{
 *     gameweek: int,
 *     total_challenges: int,
 *     answered_challenges: int,
 *     points_earned: string,
 *     max_points_possible: string,
 * }
 */
#[ApiResource(
    shortName: 'Player activity',
)]
#[Get(
    uriTemplate: '/player/{id}/activity',
    provider: PlayerActivityProvider::class,
)]
readonly final class PlayerActivityResponse
{
    /**
     * @param array<GameweekActivity> $gameweeks
     */
    public function __construct(
        public Uuid $id,
        public array $gameweeks,
        public float $overallActivity,
        public int $currentStreak,
        public int $longestStreak,
    ) {
    }

    /**
     * @param array<PlayerActivityRow> $rows
     */
    public static function fromArray(Uuid $playerId, array $rows): self
    {
        $gameweeks = array_map(
            static function (array $row): GameweekActivity {
                $total = (int) $row['total_challenges'];
                $answered = (int) $row['answered_challenges'];

                return new GameweekActivity(
                    gameweek: (int) $row['gameweek'],
                    totalChallenges: $total,
                    answeredChallenges: $answered,
                    activity: $total > 0 ? round($answered / $total, 4) : 0.0,
                    pointsEarned: (int) $row['points_earned'],
                    maxPointsPossible: (int) $row['max_points_possible'],
                );
            },
            $rows,
        );

        $totalChallenges = array_sum(array_map(static fn (GameweekActivity $gw): int => $gw->totalChallenges, $gameweeks));
        $totalAnswered = array_sum(array_map(static fn (GameweekActivity $gw): int => $gw->answeredChallenges, $gameweeks));

        return new self(
            id: $playerId,
            gameweeks: $gameweeks,
            overallActivity: $totalChallenges > 0 ? round($totalAnswered / $totalChallenges, 4) : 0.0,
            currentStreak: self::calculateCurrentStreak($gameweeks),
            longestStreak: self::calculateLongestStreak($gameweeks),
        );
    }

    /**
     * @param array<GameweekActivity> $gameweeks
     */
    private static function calculateCurrentStreak(array $gameweeks): int
    {
        $streak = 0;

        for ($i = count($gameweeks) - 1; $i >= 0; $i--) {
            if ($gameweeks[$i]->activity >= 1.0) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * @param array<GameweekActivity> $gameweeks
     */
    private static function calculateLongestStreak(array $gameweeks): int
    {
        $longest = 0;
        $current = 0;

        foreach ($gameweeks as $gw) {
            if ($gw->activity >= 1.0) {
                $current++;
                $longest = max($longest, $current);
            } else {
                $current = 0;
            }
        }

        return $longest;
    }
}
