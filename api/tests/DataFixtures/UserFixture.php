<?php
declare(strict_types=1);

namespace FantasyAcademy\API\Tests\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use FantasyAcademy\API\Entity\User;
use Psr\Clock\ClockInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

final class UserFixture extends Fixture
{
    public function __construct(
        readonly private ClockInterface $clock,
        readonly private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public const string USER_PASSWORD = 'pass';

    public const string USER_1_ID = '00000000-0000-0000-0001-000000000001';
    public const string USER_1_EMAIL = 'admin@example.com';

    public const string USER_2_ID = '00000000-0000-0000-0001-000000000002';
    public const string USER_2_EMAIL = 'user@example.com';

    public function load(ObjectManager $manager): void
    {
        $registeredAt = $this->clock->now()->modify('-1 week');

        $user1 = new User(
            Uuid::fromString(self::USER_1_ID),
            self::USER_1_EMAIL,
            $registeredAt,
            'User 1',
            true,
            [User::ROLE_ADMIN],
        );

        $hashedPassword = $this->passwordHasher->hashPassword($user1, self::USER_PASSWORD);
        $user1->changePassword($hashedPassword);

        $manager->persist($user1);


        $user2 = new User(
            Uuid::fromString(self::USER_2_ID),
            self::USER_2_EMAIL,
            $registeredAt,
            'User 2',
            true,
            [User::ROLE_USER],
        );

        $hashedPassword = $this->passwordHasher->hashPassword($user2, self::USER_PASSWORD);
        $user2->changePassword($hashedPassword);

        $manager->persist($user2);

        $manager->flush();
    }
}
