<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\ProviderInterface;
use FantasyAcademy\API\Api\ApiResource\ChallengeResponse;
use Symfony\Bundle\SecurityBundle\Security;

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

        $completedAt = null;

        if ($user !== null) {
            $completedAt = new \DateTimeImmutable();
        }

        $items = [];

        $items[] = new ChallengeResponse(
            'Name of the challenge',
            new \DateTimeImmutable('2025-06-06 12:00:00'),
            new \DateTimeImmutable('2025-09-06 12:00:00'),
            $completedAt,
        );

        return new ArrayPaginator(
            $items,
            0,
            count($items),
        );
    }
}
