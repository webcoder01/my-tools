<?php

namespace App\Core\Security\Provider\Entity;

use App\Shared\Infrastructure\AbstractEntity;
use App\Shared\Infrastructure\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: '`user`')]
class User extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface, EntityInterface
{
    #[ORM\Column(length: 50, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column]
    private ?string $password = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function addRole(string $role): void
    {
        $this->roles = \array_unique([...$this->roles, $role]);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function eraseCredentials(): void
    {
    }
}
