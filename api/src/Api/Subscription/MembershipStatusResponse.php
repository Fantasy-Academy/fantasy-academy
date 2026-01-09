<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Subscription;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use DateTimeImmutable;

#[ApiResource(
    shortName: 'Membership status',
)]
#[Get(
    uriTemplate: '/subscription/status',
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
    provider: MembershipStatusProvider::class,
)]
final class MembershipStatusResponse
{
    public function __construct(
        public bool $isActive,
        public ?string $status,
        public ?string $planId,
        public ?DateTimeImmutable $currentPeriodEnd,
        public ?DateTimeImmutable $canceledAt,
        public bool $willCancelAtPeriodEnd,
    ) {
    }

    public static function inactive(): self
    {
        return new self(
            isActive: false,
            status: null,
            planId: null,
            currentPeriodEnd: null,
            canceledAt: null,
            willCancelAtPeriodEnd: false,
        );
    }
}
