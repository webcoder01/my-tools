<?php

namespace App\AccountManager\Account\Provider\Entity;

use App\Core\Security\Infrastructure\Entity\User;
use App\Shared\Infrastructure\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'account')]
class Account extends AbstractEntity
{
  #[ORM\Column(type: 'string', length: 50)]
  private ?string $name = null;

  #[ORM\Column(type: 'decimal', precision: 7, scale: 2)]
  private ?string $balance = null;

  #[ORM\ManyToOne(targetEntity: User::class)]
  #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
  private ?User $user = null;

  public function getId(): string
  {
    return $this->id;
  }

  public function setName(string $name): void
  {
    $this->name = $name;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setBalance(string $balance): void
  {
    $this->balance = $balance;
  }

  public function getBalance(): ?string
  {
    return $this->balance;
  }

  public function setUser(User $user): void
  {
    $this->user = $user;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }
}
