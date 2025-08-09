<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use FantasyAcademy\API\Api\Response\LoggedUserResponse;
use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Value\PlayerSeasonStatistics;
use FantasyAcademy\API\Value\PlayerStatistics;
use FantasyAcademy\API\Value\PlayerSkill;
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
                points: 0,
                skills: [
                    new PlayerSkill(
                        name: 'Some skill',
                        percentage: 50,
                        percentageChange: 0,
                    ),
                ],
            ),
            seasonsStatistics: [
                new PlayerSeasonStatistics(
                    seasonNumber: 1,
                    rank: 0,
                    challengesAnswered: 4,
                    points: 0,
                    skills: [
                        new PlayerSkill(
                            name: 'Some skill',
                            percentage: 50,
                            percentageChange: 0,
                        ),
                    ],
                ),
            ]
        );
    }
}
