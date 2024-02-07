<?php

namespace App\AccountManager\Budget\Infrastructure\Entity;

use App\Core\Security\Infrastructure\Entity\User;
use App\Shared\Infrastructure\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'budget_category')]
class BudgetCategory extends AbstractEntity
{
  #[ORM\Column(type: 'string', length: 80)]
  private ?string $name = null;

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

  public function setUser(User $user): void
  {
    $this->user = $user;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }
}
