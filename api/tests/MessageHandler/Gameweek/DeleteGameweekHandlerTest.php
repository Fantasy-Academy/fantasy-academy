<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\MessageHandler\Gameweek;

use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\Gameweek;
use FantasyAcademy\API\Exceptions\GameweekNotFound;
use FantasyAcademy\API\Message\Gameweek\DeleteGameweek;
use FantasyAcademy\API\Tests\DataFixtures\GameweekFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @covers \FantasyAcademy\API\MessageHandler\Gameweek\DeleteGameweekHandler
 */
final class DeleteGameweekHandlerTest extends KernelTestCase
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

    public function testHandlerDeletesGameweek(): void
    {
        $gameweekId = Uuid::fromString(GameweekFixture::GAMEWEEK_3_ID);

        $message = new DeleteGameweek($gameweekId);

        $this->messageBus->dispatch($message);

        $this->entityManager->flush();
        $this->entityManager->clear();

        $repository = $this->entityManager->getRepository(Gameweek::class);
        $gameweek = $repository->find($gameweekId);

        $this->assertNull($gameweek);
    }

    public function testHandlerThrowsExceptionForNonExistentGameweek(): void
    {
        $nonExistentId = Uuid::v4();

        $message = new DeleteGameweek($nonExistentId);

        try {
            $this->messageBus->dispatch($message);
            $this->fail('Expected GameweekNotFound exception to be thrown');
        } catch (HandlerFailedException $exception) {
            $this->assertInstanceOf(GameweekNotFound::class, $exception->getPrevious());
        }
    }
}
