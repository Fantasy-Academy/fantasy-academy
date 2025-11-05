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

    public function load(ObjectManager $manager): void
    {
        $now = $this->clock->now();

        // Past gameweek
        $gameweek1 = new Gameweek(
            Uuid::fromString(self::GAMEWEEK_1_ID),
            season: 2025,
            number: 1,
            title: 'Opening Gameweek',
            description: 'The first gameweek of the season',
            startsAt: new DateTimeImmutable($now->modify('-2 weeks')->format('Y-m-d H:i:s')),
            endsAt: new DateTimeImmutable($now->modify('-1 week')->format('Y-m-d H:i:s')),
        );

        $manager->persist($gameweek1);

        // Current gameweek
        $gameweek2 = new Gameweek(
            Uuid::fromString(self::GAMEWEEK_2_ID),
            season: 2025,
            number: 2,
            title: 'Midweek Madness',
            description: 'A challenging midweek gameweek',
            startsAt: new DateTimeImmutable($now->modify('-1 day')->format('Y-m-d H:i:s')),
            endsAt: new DateTimeImmutable($now->modify('+6 days')->format('Y-m-d H:i:s')),
        );

        $manager->persist($gameweek2);

        // Future gameweek
        $gameweek3 = new Gameweek(
            Uuid::fromString(self::GAMEWEEK_3_ID),
            season: 2025,
            number: 3,
            title: null,
            description: null,
            startsAt: new DateTimeImmutable($now->modify('+1 week')->format('Y-m-d H:i:s')),
            endsAt: new DateTimeImmutable($now->modify('+2 weeks')->format('Y-m-d H:i:s')),
        );

        $manager->persist($gameweek3);

        $manager->flush();
    }
}
