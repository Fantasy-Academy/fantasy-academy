<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Services;

use DateTimeImmutable;
use DateTimeZone;
use FantasyAcademy\API\Services\GameWeekService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FantasyAcademy\API\Services\GameWeekService
 */
final class GameWeekServiceTest extends TestCase
{
    /**
     * @param non-empty-string $now
     * @param non-empty-string $expected
     * @param null|non-empty-string $timezone
     */
    #[DataProvider('lastMondayCutoffProvider')]
    public function testGetLastMondayCutoff(string $now, string $expected, ?string $timezone = null): void
    {
        $dateTime = $timezone !== null
            ? new DateTimeImmutable($now, new DateTimeZone($timezone))
            : new DateTimeImmutable($now);

        $result = GameWeekService::getLastMondayCutoff($dateTime);

        $this->assertEquals($expected, $result->format('Y-m-d H:i:s'));

        if ($timezone !== null) {
            $this->assertEquals($timezone, $result->getTimezone()->getName());
        }
    }

    /**
     * @return iterable<string, array{now: string, expected: string, timezone?: string}>
     */
    public static function lastMondayCutoffProvider(): iterable
    {
        yield 'Monday should return previous Monday' => [
            'now' => '2025-06-09 15:30:00',
            'expected' => '2025-06-02 23:59:59',
        ];

        yield 'Tuesday should return most recent Monday' => [
            'now' => '2025-06-10 10:00:00',
            'expected' => '2025-06-09 23:59:59',
        ];

        yield 'Wednesday should return most recent Monday' => [
            'now' => '2025-06-11 08:15:30',
            'expected' => '2025-06-09 23:59:59',
        ];

        yield 'Thursday should return most recent Monday' => [
            'now' => '2025-06-12 12:00:00',
            'expected' => '2025-06-09 23:59:59',
        ];

        yield 'Friday should return most recent Monday' => [
            'now' => '2025-06-13 16:45:00',
            'expected' => '2025-06-09 23:59:59',
        ];

        yield 'Saturday should return most recent Monday' => [
            'now' => '2025-06-14 20:30:00',
            'expected' => '2025-06-09 23:59:59',
        ];

        yield 'Sunday should return most recent Monday' => [
            'now' => '2025-06-08 18:45:00',
            'expected' => '2025-06-02 23:59:59',
        ];

        yield 'Tuesday at midnight should return Monday' => [
            'now' => '2025-06-10 00:00:00',
            'expected' => '2025-06-09 23:59:59',
        ];

        yield 'Tuesday at end of day should return Monday' => [
            'now' => '2025-06-10 23:59:59',
            'expected' => '2025-06-09 23:59:59',
        ];

        yield 'Timezone should be preserved (Europe/Prague)' => [
            'now' => '2025-06-10 10:00:00',
            'expected' => '2025-06-09 23:59:59',
            'timezone' => 'Europe/Prague',
        ];

        yield 'Timezone should be preserved (America/New_York)' => [
            'now' => '2025-06-10 10:00:00',
            'expected' => '2025-06-09 23:59:59',
            'timezone' => 'America/New_York',
        ];

        yield 'Timezone should be preserved (Asia/Tokyo)' => [
            'now' => '2025-06-10 10:00:00',
            'expected' => '2025-06-09 23:59:59',
            'timezone' => 'Asia/Tokyo',
        ];
    }
}
