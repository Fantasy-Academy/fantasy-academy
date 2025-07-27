<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\ProviderInterface;
use FantasyAcademy\API\Api\ApiResource\ChallengeResponse;
use FantasyAcademy\API\Exceptions\ChallengeExpired;

/**
 * @implements ProviderInterface<ChallengeResponse>
 */
readonly final class ChallengesProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ArrayPaginator
    {
        $items = [];

        $items[] = new ChallengeResponse(
            'Name of the challenge',
            new \DateTimeImmutable('2025-06-06 12:00:00'),
            new \DateTimeImmutable('2025-09-06 12:00:00'),
            null,
        );

        return new ArrayPaginator(
            $items,
            0,
            count($items),
        );
    }
}
