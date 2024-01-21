<?php

namespace App\AccountManager\Budget\Infrastructure\Entity;

use App\Shared\Infrastructure\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'budget_category')]
final class BudgetCategory extends AbstractEntity
{
  #[ORM\Column(type: 'string', length: 80)]
  private ?string $name = null;

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
}
