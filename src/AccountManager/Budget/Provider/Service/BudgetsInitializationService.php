<?php

namespace App\AccountManager\Budget\Provider\Service;

use App\AccountManager\Budget\Infrastructure\Entity\Budget;
use App\AccountManager\Budget\Infrastructure\Entity\BudgetCategory;
use App\AccountManager\Budget\Infrastructure\Entity\BudgetType;
use App\AccountManager\Budget\Port\Output\BudgetsInitializationServiceInterface;
use App\Shared\Provider\AbstractService;
use DateTime;

class BudgetsInitializationService extends AbstractService implements BudgetsInitializationServiceInterface
{
  /** @var BudgetType[] $budgetTypes */
  private array $budgetTypes;

  private int $month;
  private int $year;
  private string $userId;

  public function persistFirstEverBudgetsOfUser(string $userId, DateTime $budgetsDate): void
  {
    $this->initializeVariables($userId, $budgetsDate);
    $this->persistFirstEverBudgets();
    $this->flushAndClear();
  }

  public function persistBudgetsOfUserThatAreACopyOfPreviousOnes(string $userId, DateTime $budgetsDate): void
  {
    $this->initializeVariables($userId, $budgetsDate);
    $previousBudgets = $this->getPreviousBudgetsOfTypes();
    $this->persistBudgetsWithPreviousOnes($previousBudgets);
    $this->flushAndClear();
  }

  private function initializeVariables(string $userId, DateTime $budgetsDate): void
  {
    $this->userId = $userId;
    $budgetCategories = $this->getBudgetCategoriesOfUser();
    $this->budgetTypes = $this->getBudgetTypesOfUser($budgetCategories);
    $this->setMonthAndYearFromGivenDate($budgetsDate);
  }

  /**
   * @return BudgetCategory[]
   */
  private function getBudgetCategoriesOfUser(): array
  {
    return $this->entityManager
      ->getRepository(BudgetCategory::class)
      ->findBy(['user' => $this->userId])
    ;
  }

  /**
   * @return BudgetType[]
   */
  private function getBudgetTypesOfUser(array $budgetCategories): array
  {
    return $this->entityManager
      ->getRepository(BudgetType::class)
      ->findBy(['category' => $budgetCategories])
    ;
  }

  private function setMonthAndYearFromGivenDate(DateTime $date): void
  {
    $this->month = (int) $date->format('n');
    $this->year = (int) $date->format('Y');
  }

  private function persistFirstEverBudgets(): void
  {
    foreach ($this->budgetTypes as $type) {
      $budget = new Budget();
      $budget->setMonth($this->month);
      $budget->setYear($this->year);
      $budget->setType($type);
      $budget->setAssignedAmount('0.00');
      $budget->setAvailableAmount('0.00');

      $this->entityManager->persist($budget);
    }
  }

  /**
   * @return Budget[]
   */
  private function getPreviousBudgetsOfTypes(): array
  {
    return $this->entityManager
      ->getRepository(Budget::class)
      ->findBy(['type' => $this->budgetTypes])
      ;
  }

  /**
   * @param Budget[] $previousBudgets
   */
  private function persistBudgetsWithPreviousOnes(array $previousBudgets): void
  {
    foreach ($previousBudgets as $previousBudget) {
      $budget = new Budget();
      $budget->setMonth($this->month);
      $budget->setYear($this->year);
      $budget->setType($previousBudget->getType());
      $budget->setAssignedAmount($previousBudget->getAssignedAmount());
      $budget->setAvailableAmount($previousBudget->getAvailableAmount());

      $this->entityManager->persist($budget);
    }
  }

  private function flushAndClear(): void
  {
    $this->entityManager->flush();
    $this->entityManager->clear();
  }
}
