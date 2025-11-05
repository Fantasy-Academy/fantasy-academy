<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api\Gameweeks;

use FantasyAcademy\API\Tests\ApiTestCase;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;

/**
 * @covers \FantasyAcademy\API\Api\Gameweeks\GameweeksProvider
 * @covers \FantasyAcademy\API\Api\Gameweeks\GameweeksResponse
 * @covers \FantasyAcademy\API\Api\Gameweeks\GameweekResponse
 */
final class GameweeksTest extends ApiTestCase
{
    public function testNormalScenario(): void
    {
        $client = self::createClient();

        /** @var MockClock $clock */
        $clock = self::getContainer()->get(ClockInterface::class);
        $clock->modify('2025-06-06 12:00:00 UTC');

        // Default time: 2025-06-06 12:00:00
        // GW2 ended 1 week ago (previous)
        // GW3 started 1 day ago, ends in 6 days (current)
        // GW4 starts in 1 week (next)

        $response = $client->request('GET', '/api/gameweeks');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/normal_scenario.json',
            $response->getContent(),
        );
    }

    public function testSeasonBreak(): void
    {
        $client = self::createClient();

        /** @var MockClock $clock */
        $clock = self::getContainer()->get(ClockInterface::class);

        // Set time between GW2 end and GW3 start (season break)
        // GW2 ends at: 2025-06-06 12:00:00 - 1 week = 2025-05-30 12:00:00
        // GW3 starts at: 2025-06-06 12:00:00 - 1 day = 2025-06-05 12:00:00
        // Set time to: 2025-06-01 12:00:00 (between GW2 end and GW3 start)
        $clock->modify('2025-06-01 12:00:00 UTC');

        $response = $client->request('GET', '/api/gameweeks');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/season_break.json',
            $response->getContent(),
        );
    }

    public function testStartOfSeason(): void
    {
        $client = self::createClient();

        /** @var MockClock $clock */
        $clock = self::getContainer()->get(ClockInterface::class);

        // Set time during GW1 (first gameweek, no previous)
        // GW1 starts at: 2025-06-06 12:00:00 - 4 weeks = 2025-05-09 12:00:00
        // GW1 ends at: 2025-06-06 12:00:00 - 3 weeks = 2025-05-16 12:00:00
        // Set time to: 2025-05-10 12:00:00 (during GW1)
        $clock->modify('2025-05-10 12:00:00 UTC');

        $response = $client->request('GET', '/api/gameweeks');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/start_of_season.json',
            $response->getContent(),
        );
    }

    public function testEndOfSeason(): void
    {
        $client = self::createClient();

        /** @var MockClock $clock */
        $clock = self::getContainer()->get(ClockInterface::class);

        // Set time during GW5 (last gameweek, no next)
        // GW5 starts at: 2025-06-06 12:00:00 + 3 weeks = 2025-06-27 12:00:00
        // GW5 ends at: 2025-06-06 12:00:00 + 4 weeks = 2025-07-04 12:00:00
        // Set time to: 2025-06-28 12:00:00 (during GW5)
        $clock->modify('2025-06-28 12:00:00 UTC');

        $response = $client->request('GET', '/api/gameweeks');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/end_of_season.json',
            $response->getContent(),
        );
    }
}
