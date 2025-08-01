<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\ProviderInterface;
use FantasyAcademy\API\Api\ApiResource\ChallengeResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProviderInterface<ChallengeResponse>
 */
readonly final class ChallengesProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ArrayPaginator
    {
        $user = $this->security->getUser();

        $answeredAt = null;

        if ($user !== null) {
            $answeredAt = new \DateTimeImmutable();
        }

        $items = [
            new ChallengeResponse(
                id: Uuid::fromString('52a9de01-5f68-4c65-8443-ff04e1fe2642'),
                name: 'Name of the challenge',
                shortDescription: 'Short description',
                description: 'Description of the challenge',
                image: 'https://placecats.com/800/600',
                addedAt: new \DateTimeImmutable('2025-06-06 12:00:00'),
                startsAt: new \DateTimeImmutable('2025-07-29 12:00:00'),
                expiresAt: new \DateTimeImmutable('2025-09-06 12:00:00'),
                answeredAt: $answeredAt,
                isStarted: true,
                isExpired: false,
                isAnswered: $user !== null,
                isEvaluated: false,
            ),
            new ChallengeResponse(
                id: Uuid::fromString('52a9de01-5f68-4c65-8443-ff04e1fe264a'),
                name: 'Name of the other challenge',
                shortDescription: 'Short description',
                description: 'Description of the challenge',
                image: 'https://placecats.com/800/600',
                addedAt: new \DateTimeImmutable('2025-06-06 12:00:00'),
                startsAt: new \DateTimeImmutable('2025-07-29 12:00:00'),
                expiresAt: new \DateTimeImmutable('2025-09-06 12:00:00'),
                answeredAt: $answeredAt,
                isStarted: true,
                isExpired: false,
                isAnswered: $user !== null,
                isEvaluated: false,
            ),
        ];

        return new ArrayPaginator(
            $items,
            0,
            count($items),
        );
    }
}
