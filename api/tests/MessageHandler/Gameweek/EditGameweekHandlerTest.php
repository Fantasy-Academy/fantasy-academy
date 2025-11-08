<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\MessageHandler\Gameweek;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\Gameweek;
use FantasyAcademy\API\Message\Gameweek\EditGameweek;
use FantasyAcademy\API\Tests\DataFixtures\GameweekFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @covers \FantasyAcademy\API\MessageHandler\Gameweek\EditGameweekHandler
 */
final class EditGameweekHandlerTest extends KernelTestCase
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

    public function testHandlerUpdatesGameweek(): void
    {
        $gameweekId = Uuid::fromString(GameweekFixture::GAMEWEEK_1_ID);

        $message = new EditGameweek(
            id: $gameweekId,
            season: 2026,
            number: 99,
            title: 'Updated Title',
            description: 'Updated Description',
            startsAt: new DateTimeImmutable('2026-06-01 00:00:00'),
            endsAt: new DateTimeImmutable('2026-06-07 23:59:59'),
        );

        $this->messageBus->dispatch($message);

        $this->entityManager->flush();
        $this->entityManager->clear();

        $repository = $this->entityManager->getRepository(Gameweek::class);
        $gameweek = $repository->find($gameweekId);

        $this->assertInstanceOf(Gameweek::class, $gameweek);
        $this->assertEquals($gameweekId, $gameweek->id);
        $this->assertSame(2026, $gameweek->season);
        $this->assertSame(99, $gameweek->number);
        $this->assertSame('Updated Title', $gameweek->title);
        $this->assertSame('Updated Description', $gameweek->description);
        $this->assertEquals(new DateTimeImmutable('2026-06-01 00:00:00'), $gameweek->startsAt);
        $this->assertEquals(new DateTimeImmutable('2026-06-07 23:59:59'), $gameweek->endsAt);
    }

    public function testHandlerUpdatesGameweekWithNullValues(): void
    {
        $gameweekId = Uuid::fromString(GameweekFixture::GAMEWEEK_2_ID);

        $message = new EditGameweek(
            id: $gameweekId,
            season: 2027,
            number: 1,
            title: null,
            description: null,
            startsAt: new DateTimeImmutable('2027-01-01 00:00:00'),
            endsAt: new DateTimeImmutable('2027-01-07 23:59:59'),
        );

        $this->messageBus->dispatch($message);

        $this->entityManager->flush();
        $this->entityManager->clear();

        $repository = $this->entityManager->getRepository(Gameweek::class);
        $gameweek = $repository->find($gameweekId);

        $this->assertInstanceOf(Gameweek::class, $gameweek);
        $this->assertNull($gameweek->title);
        $this->assertNull($gameweek->description);
    }
}
