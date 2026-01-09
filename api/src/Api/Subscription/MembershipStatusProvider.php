<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Subscription;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Services\Subscription\SubscriptionChecker;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProviderInterface<MembershipStatusResponse>
 */
readonly final class MembershipStatusProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private SubscriptionChecker $subscriptionChecker,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): MembershipStatusResponse
    {
        $user = $this->security->getUser();
        assert($user instanceof User);

        $subscription = $this->subscriptionChecker->getActiveSubscription($user->id);

        if ($subscription === null) {
            return MembershipStatusResponse::inactive();
        }

        return new MembershipStatusResponse(
            isActive: true,
            status: $subscription->status,
            planId: $subscription->planId,
            currentPeriodEnd: $subscription->currentPeriodEnd,
            canceledAt: $subscription->canceledAt,
            willCancelAtPeriodEnd: $subscription->cancelAtPeriodEnd,
        );
    }
}
