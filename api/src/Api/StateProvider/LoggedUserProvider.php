<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use FantasyAcademy\API\Api\ApiResource\LoggedUserResponse;
use FantasyAcademy\API\Api\ApiResource\PlayerSeasonStatistics;
use FantasyAcademy\API\Api\ApiResource\PlayerStatistics;
use FantasyAcademy\API\Api\ApiResource\Skill;
use FantasyAcademy\API\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProviderInterface<LoggedUserResponse>
 */
readonly final class LoggedUserProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object
    {
        $user = $this->security->getUser();
        assert($user instanceof User);

        return new LoggedUserResponse(
            id: $user->id->toString(),
            name: $user->name ?? '',
            email: $user->email,
            availableChallenges: 2,
            completedChallenges: 2,
            registeredAt: $user->registeredAt,
            overallStatistics: new PlayerStatistics(
                rank: 3,
                challengesAnswered: 4,
                points: 2000,
                skills: [
                    new Skill(
                        name: 'Some skill',
                        percentage: 50,
                        percentageChange: 2,
                    ),
                ],
            ),
            seasonsStatistics: [
                new PlayerSeasonStatistics(
                    seasonNumber: 1,
                    rank: 3,
                    challengesAnswered: 4,
                    points: 2000,
                    skills: [
                        new Skill(
                            name: 'Some skill',
                            percentage: 50,
                            percentageChange: 2,
                        ),
                    ],
                ),
            ]
        );
    }
}
