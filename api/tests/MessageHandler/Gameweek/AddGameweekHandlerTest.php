<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\MessageHandler\Gameweek;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\Gameweek;
use FantasyAcademy\API\Message\Gameweek\AddGameweek;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @covers \FantasyAcademy\API\MessageHandler\Gameweek\AddGameweekHandler
 */
final class AddGameweekHandlerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private MessageBusInterface $messageBus;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->messageBus = $container->get(MessageBusInterface::class);
    }

    public function testHandlerCreatesGameweek(): void
    {
        $message = new AddGameweek(
            season: 2026,
            number: 10,
            title: 'Test Gameweek',
            description: 'Test Description',
            startsAt: new DateTimeImmutable('2026-01-01 00:00:00'),
            endsAt: new DateTimeImmutable('2026-01-07 23:59:59'),
        );

        $this->messageBus->dispatch($message);

        $this->entityManager->flush();
        $this->entityManager->clear();

        $repository = $this->entityManager->getRepository(Gameweek::class);
        $gameweeks = $repository->findBy(['season' => 2026, 'number' => 10]);

        $this->assertCount(1, $gameweeks);

        $gameweek = $gameweeks[0];

        $this->assertSame(2026, $gameweek->season);
        $this->assertSame(10, $gameweek->number);
        $this->assertSame('Test Gameweek', $gameweek->title);
        $this->assertSame('Test Description', $gameweek->description);
        $this->assertEquals(new DateTimeImmutable('2026-01-01 00:00:00'), $gameweek->startsAt);
        $this->assertEquals(new DateTimeImmutable('2026-01-07 23:59:59'), $gameweek->endsAt);
    }

    public function testHandlerCreatesGameweekWithNullTitleAndDescription(): void
    {
        $message = new AddGameweek(
            season: 2026,
            number: 11,
            title: null,
            description: null,
            startsAt: new DateTimeImmutable('2026-01-08 00:00:00'),
            endsAt: new DateTimeImmutable('2026-01-14 23:59:59'),
        );

        $this->messageBus->dispatch($message);

        $this->entityManager->flush();
        $this->entityManager->clear();

        $repository = $this->entityManager->getRepository(Gameweek::class);
        $gameweeks = $repository->findBy(['season' => 2026, 'number' => 11]);

        $this->assertCount(1, $gameweeks);

        $gameweek = $gameweeks[0];

        $this->assertNull($gameweek->title);
        $this->assertNull($gameweek->description);
    }
}
