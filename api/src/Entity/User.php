<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use FantasyAcademy\API\Events\UserRegistered;
use JetBrains\PhpStorm\Immutable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, EntityWithEvents
{
    use HasEvents;

    public const string ROLE_ADMIN = 'ROLE_ADMIN';
    public const string ROLE_USER = 'ROLE_USER';

    #[Immutable]
    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    public null|DateTimeImmutable $deactivatedAt = null;

    #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
    #[Column]
    public string $password = '';

    #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    public null|DateTimeImmutable $lastActivity = null;

    public function __construct(
        #[Id]
        #[Immutable]
        #[Column(type: UuidType::NAME, unique: true)]
        public Uuid $id,

        #[Column(length: 180, unique: true)]
        readonly public string $email,

        #[Column(type: Types::DATETIME_IMMUTABLE)]
        readonly public DateTimeImmutable $registeredAt,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(nullable: true)]
        public null|string $name = null,

        #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
        #[Column(options: ['default' => true])]
        public bool $confirmed = true,

        /** @var array<string> */
        #[Column(type: Types::JSON)]
        readonly private array $roles = [],
    ) {
        $this->recordThat(
            new UserRegistered($id),
        );
    }

    public function changePassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return array<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getPassword(): string
    {
        // Just to satisfy the interface ...
        return $this->password;
    }

    public function eraseCredentials(): void
    {
        // Just to satisfy the interface ...
    }

    public function displayName(): string
    {
        return $this->name ?? $this->email;
    }

    public function editProfile(
        null|string $name,
    ): void {
        $this->name = $name;
    }

    public function refreshLastActivity(DateTimeImmutable $now): void
    {
        $this->lastActivity = $now;
    }
}
