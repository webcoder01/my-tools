<?php

namespace App\AccountManager\Transaction\Provider\Entity;

use App\AccountManager\Account\Provider\Entity\Account;
use App\AccountManager\Budget\Infrastructure\Entity\BudgetType;
use App\Shared\Infrastructure\AbstractEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'transaction')]
class Transaction extends AbstractEntity
{
  #[ORM\Column(type: 'string', length: 80)]
  private ?string $payee = null;

  #[ORM\Column(type: 'decimal', precision: 7, scale: 2)]
  private ?string $amount = null;

  #[ORM\Column(type: 'datetime')]
  private DateTime $creationDate;

  #[ORM\Column(type: 'string', length: 200, nullable: true)]
  private ?string $comment = null;

  #[ORM\Column(type: 'boolean', options: ['default' => false])]
  private bool $checked = false;

  #[ORM\ManyToOne(targetEntity: BudgetType::class)]
  #[ORM\JoinColumn(name: 'budget_type_id', referencedColumnName: 'id')]
  private ?BudgetType $budgetType = null;

  #[ORM\ManyToOne(targetEntity: Account::class)]
  #[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id')]
  private ?Account $account = null;

  public function __construct()
  {
    parent::__construct();

    $this->creationDate = new DateTime();
  }

  public function getId(): string
  {
    return $this->id;
  }

  public function setPayee(string $payee): void
  {
    $this->payee = $payee;
  }

  public function getPayee(): ?string
  {
    return $this->payee;
  }

  public function setAmount(string $amount): void
  {
    $this->amount = $amount;
  }

  public function getAmount(): ?string
  {
    return $this->amount;
  }

  public function getCreationDate(): DateTime
  {
    return $this->creationDate;
  }

  public function setComment(?string $comment): void
  {
    $this->comment = $comment;
  }

  public function getComment(): ?string
  {
    return $this->comment;
  }

  public function setChecked(bool $checked): void
  {
    $this->checked = $checked;
  }

  public function isChecked(): bool
  {
    return $this->checked;
  }

  public function setBudgetType(BudgetType $budgetType): void
  {
    $this->budgetType = $budgetType;
  }

  public function getBudgetType(): ?BudgetType
  {
    return $this->budgetType;
  }

  public function setAccount(Account $account): void
  {
    $this->account = $account;
  }

  public function getAccount(): ?Account
  {
    return $this->account;
  }
}
