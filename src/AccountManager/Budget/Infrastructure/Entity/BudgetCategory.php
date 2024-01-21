<?php

namespace App\AccountManager\Budget\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'budget_category')]
final class BudgetCategory
{
  #[ORM\Id]
  #[ORM\Column(type: 'string',  length: 36)]
  private string $id;

  #[ORM\Column(type: 'string', length: 80)]
  private ?string $name = null;

  public function __construct()
  {
    $this->id = Uuid::v4()->toRfc4122();
  }

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
