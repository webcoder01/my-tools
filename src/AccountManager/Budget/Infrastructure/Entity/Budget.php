<?php

namespace App\AccountManager\Budget\Infrastructure\Entity;

use App\Shared\Infrastructure\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'budget')]
final class Budget extends AbstractEntity
{
  #[ORM\Column(type: 'decimal', precision: 7, scale: 2)]
  private ?string $assignedAmount = null;

  #[ORM\Column(type: 'decimal', precision: 7, scale: 2)]
  private ?string $availableAmount = null;

  #[ORM\Column(type: 'smallint')]
  private ?int $month = null;

  #[ORM\Column(type: 'smallint')]
  private ?int $year = null;

  #[ORM\ManyToOne(targetEntity: BudgetType::class)]
  #[ORM\JoinColumn(name: 'type_id', referencedColumnName: 'id')]
  private ?BudgetType $type = null;

  public function getId(): string
  {
    return $this->id;
  }

  public function setAssignedAmount(float $assignedAmount): void
  {
    $this->assignedAmount = $assignedAmount;
  }

  public function getAssignedAmount(): ?string
  {
    return $this->assignedAmount;
  }

  public function setAvailableAmount(float $availableAmount): void
  {
    $this->availableAmount = $availableAmount;
  }

  public function getAvailableAmount(): ?string
  {
    return $this->availableAmount;
  }

  public function setType(BudgetType $type): void
  {
    $this->type = $type;
  }

  public function getType(): ?BudgetType
  {
    return $this->type;
  }

  public function setMonth(int $month): void
  {
    $this->month = $month;
  }

  public function getMonth(): ?int
  {
    return $this->month;
  }

  public function setYear(int $year): void
  {
    $this->year = $year;
  }

  public function getYear(): ?int
  {
    return $this->year;
  }
}
