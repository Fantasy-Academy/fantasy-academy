<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use FantasyAcademy\API\Entity\Subscription;
use FantasyAcademy\API\Entity\User;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

final class SubscriptionFixture extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    // Active subscription for user@example.com (USER_2)
    public const string ACTIVE_SUBSCRIPTION_ID = '00000000-0000-0000-0002-000000000001';
    public const string ACTIVE_STRIPE_SUBSCRIPTION_ID = 'sub_active_test_123';
    public const string ACTIVE_STRIPE_CUSTOMER_ID = 'cus_active_test_123';

    // Canceled but still in period subscription for user3@example.com (USER_3)
    public const string CANCELED_SUBSCRIPTION_ID = '00000000-0000-0000-0002-000000000002';
    public const string CANCELED_STRIPE_SUBSCRIPTION_ID = 'sub_canceled_test_456';
    public const string CANCELED_STRIPE_CUSTOMER_ID = 'cus_canceled_test_456';

    // Expired subscription for user4@example.com (USER_4)
    public const string EXPIRED_SUBSCRIPTION_ID = '00000000-0000-0000-0002-000000000003';
    public const string EXPIRED_STRIPE_SUBSCRIPTION_ID = 'sub_expired_test_789';
    public const string EXPIRED_STRIPE_CUSTOMER_ID = 'cus_expired_test_789';

    // admin@example.com (USER_1) has no subscription - for testing non-member scenarios

    public function load(ObjectManager $manager): void
    {
        $now = $this->clock->now();

        // Active subscription for USER_2 (user@example.com)
        $user2 = $manager->getRepository(User::class)->find(Uuid::fromString(UserFixture::USER_2_ID));
        assert($user2 instanceof User);
        $user2->updateStripeCustomerId(self::ACTIVE_STRIPE_CUSTOMER_ID);

        $activeSubscription = new Subscription(
            id: Uuid::fromString(self::ACTIVE_SUBSCRIPTION_ID),
            user: $user2,
            stripeSubscriptionId: self::ACTIVE_STRIPE_SUBSCRIPTION_ID,
            stripeCustomerId: self::ACTIVE_STRIPE_CUSTOMER_ID,
            planId: 'price_monthly_test',
            currentPeriodStart: $now->modify('-15 days'),
            currentPeriodEnd: $now->modify('+15 days'),
            status: Subscription::STATUS_ACTIVE,
            createdAt: $now->modify('-1 month'),
        );
        $manager->persist($activeSubscription);

        // Canceled but still active subscription for USER_3 (user3@example.com)
        $user3 = $manager->getRepository(User::class)->find(Uuid::fromString(UserFixture::USER_3_ID));
        assert($user3 instanceof User);
        $user3->updateStripeCustomerId(self::CANCELED_STRIPE_CUSTOMER_ID);

        $canceledSubscription = new Subscription(
            id: Uuid::fromString(self::CANCELED_SUBSCRIPTION_ID),
            user: $user3,
            stripeSubscriptionId: self::CANCELED_STRIPE_SUBSCRIPTION_ID,
            stripeCustomerId: self::CANCELED_STRIPE_CUSTOMER_ID,
            planId: 'price_yearly_test',
            currentPeriodStart: $now->modify('-10 days'),
            currentPeriodEnd: $now->modify('+20 days'),
            status: Subscription::STATUS_ACTIVE,
            createdAt: $now->modify('-2 months'),
        );
        $canceledSubscription->updateFromStripe(
            status: Subscription::STATUS_ACTIVE,
            currentPeriodStart: $now->modify('-10 days'),
            currentPeriodEnd: $now->modify('+20 days'),
            canceledAt: $now->modify('-5 days'),
            cancelAtPeriodEnd: true,
            now: $now,
        );
        $manager->persist($canceledSubscription);

        // Expired subscription for USER_4 (user4@example.com)
        $user4 = $manager->getRepository(User::class)->find(Uuid::fromString(UserFixture::USER_4_ID));
        assert($user4 instanceof User);
        $user4->updateStripeCustomerId(self::EXPIRED_STRIPE_CUSTOMER_ID);

        $expiredSubscription = new Subscription(
            id: Uuid::fromString(self::EXPIRED_SUBSCRIPTION_ID),
            user: $user4,
            stripeSubscriptionId: self::EXPIRED_STRIPE_SUBSCRIPTION_ID,
            stripeCustomerId: self::EXPIRED_STRIPE_CUSTOMER_ID,
            planId: 'price_monthly_test',
            currentPeriodStart: $now->modify('-45 days'),
            currentPeriodEnd: $now->modify('-15 days'),
            status: Subscription::STATUS_CANCELED,
            createdAt: $now->modify('-3 months'),
        );
        $manager->persist($expiredSubscription);

        $manager->flush();
    }

    /**
     * @return array<class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
