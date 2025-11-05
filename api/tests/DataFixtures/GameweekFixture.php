<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\DataFixtures;

use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use FantasyAcademy\API\Entity\Gameweek;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

final class GameweekFixture extends Fixture
{
    public function __construct(
        readonly private ClockInterface $clock,
    ) {
    }

    public const string GAMEWEEK_1_ID = '00000000-0000-0000-0003-000000000001';
    public const string GAMEWEEK_2_ID = '00000000-0000-0000-0003-000000000002';
    public const string GAMEWEEK_3_ID = '00000000-0000-0000-0003-000000000003';
    public const string GAMEWEEK_4_ID = '00000000-0000-0000-0003-000000000004';
    public const string GAMEWEEK_5_ID = '00000000-0000-0000-0003-000000000005';

    public function load(ObjectManager $manager): void
    {
        $now = $this->clock->now();

        // Past gameweek 1 - ended 3 weeks ago
        $gameweek1 = new Gameweek(
            Uuid::fromString(self::GAMEWEEK_1_ID),
            season: 2025,
            number: 1,
            title: 'Opening Gameweek',
            description: 'The first gameweek of the season',
            startsAt: new DateTimeImmutable($now->modify('-4 weeks')->format('Y-m-d H:i:s')),
            endsAt: new DateTimeImmutable($now->modify('-3 weeks')->format('Y-m-d H:i:s')),
        );

        $manager->persist($gameweek1);

        // Past gameweek 2 - most recent past, ended 1 week ago (this is "previous")
        $gameweek2 = new Gameweek(
            Uuid::fromString(self::GAMEWEEK_2_ID),
            season: 2025,
            number: 2,
            title: 'Midweek Madness',
            description: 'A challenging midweek gameweek',
            startsAt: new DateTimeImmutable($now->modify('-2 weeks')->format('Y-m-d H:i:s')),
            endsAt: new DateTimeImmutable($now->modify('-1 week')->format('Y-m-d H:i:s')),
        );

        $manager->persist($gameweek2);

        // Current gameweek 3 - started 1 day ago, ends in 6 days (this is "current")
        $gameweek3 = new Gameweek(
            Uuid::fromString(self::GAMEWEEK_3_ID),
            season: 2025,
            number: 3,
            title: 'Derby Week',
            description: 'Big matches this week',
            startsAt: new DateTimeImmutable($now->modify('-1 day')->format('Y-m-d H:i:s')),
            endsAt: new DateTimeImmutable($now->modify('+6 days')->format('Y-m-d H:i:s')),
        );

        $manager->persist($gameweek3);

        // Future gameweek 4 - starts in 1 week (this is "next")
        $gameweek4 = new Gameweek(
            Uuid::fromString(self::GAMEWEEK_4_ID),
            season: 2025,
            number: 4,
            title: null,
            description: null,
            startsAt: new DateTimeImmutable($now->modify('+1 week')->format('Y-m-d H:i:s')),
            endsAt: new DateTimeImmutable($now->modify('+2 weeks')->format('Y-m-d H:i:s')),
        );

        $manager->persist($gameweek4);

        // Future gameweek 5 - starts in 3 weeks
        $gameweek5 = new Gameweek(
            Uuid::fromString(self::GAMEWEEK_5_ID),
            season: 2025,
            number: 5,
            title: 'Final Push',
            description: 'Last gameweek before break',
            startsAt: new DateTimeImmutable($now->modify('+3 weeks')->format('Y-m-d H:i:s')),
            endsAt: new DateTimeImmutable($now->modify('+4 weeks')->format('Y-m-d H:i:s')),
        );

        $manager->persist($gameweek5);

        $manager->flush();
    }
}
