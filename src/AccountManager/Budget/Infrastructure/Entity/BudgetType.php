<?php

namespace App\AccountManager\Budget\Infrastructure\Entity;

use App\Shared\Infrastructure\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'budget_type')]
class BudgetType extends AbstractEntity
{
  #[ORM\Column(type: 'string', length: 80)]
  private ?string $name = null;

  #[ORM\ManyToOne(targetEntity: BudgetCategory::class)]
  #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id')]
  private ?BudgetCategory $category = null;

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

  public function setCategory(BudgetCategory $category): void
  {
    $this->category = $category;
  }

  public function getCategory(): ?BudgetCategory
  {
    return $this->category;
  }
}
