<?php

namespace App\AccountManager\Budget\Domain\UseCase;

use App\AccountManager\Budget\Port\Input\BudgetViewerUseCaseInterface;
use App\AccountManager\Budget\Port\Output\BudgetViewerQueryInterface;
use DateInterval;
use DateTime;

final class BudgetViewerUseCase implements BudgetViewerUseCaseInterface
{
  private BudgetViewerQueryInterface $budgetViewerQuery;

  public function __construct(BudgetViewerQueryInterface $budgetViewerQuery)
  {
    $this->budgetViewerQuery = $budgetViewerQuery;
  }

  /**
   * Returns budget categories
   *
   * @return array{
   *   array{
   *     id: string,
   *     name: string,
   *     assigned_amount: (string | null),
   *     available_amount: (string | null),
   *     budgets: array{
   *       id: string,
   *       name: string,
   *       assigned_amount: (string | null),
   *       available_amount: (string | null),
   *       comment: string,
   *     }
   *   }
   * }
   */
  public function getViewOfMonth(string $userId, int $month, int $year): array
  {
    $categoriesFromQuery = $this->budgetViewerQuery->getCategories($userId, $month, $year);
    $budgetsFromQuery = $this->budgetViewerQuery->getBudgets($userId, $month, $year);
    $budgets = [];

    foreach ($categoriesFromQuery as $category) {
      $budgets[] = [
        'id' => $category['id'],
        'name' => $category['name'],
        'assigned_amount' => $category['assigned_amount'],
        'available_amount' => $category['available_amount'],
        'budgets' => $this->getBudgetsOfCategory($category['id'], $budgetsFromQuery),
      ];
    }

    return $budgets;
  }

  private function getBudgetsOfCategory(string $categoryId, array $budgetsFromQuery): array
  {
    $budgetsOfCategory = array_filter($budgetsFromQuery, fn($budget) => $budget['category_id'] === $categoryId);
    $budgets = [];

    foreach ($budgetsOfCategory as $budgetOfCategory) {
      $budgets[] = [
        'id' => $budgetOfCategory['id'],
        'name' => $budgetOfCategory['name'],
        'assigned_amount' => $budgetOfCategory['assigned_amount'],
        'available_amount' => $budgetOfCategory['available_amount'],
      ];
    }

    return $budgets;
  }

  /**
   * @return array{
   *   previous: DateTime,
   *   current: DateTime,
   *   next: DateTime,
   * }
   */
  public function getNavigationDates(DateTime $currentDate): array
  {
    $oneMonthInterval = DateInterval::createFromDateString('1 month');

    return [
      'previous' => (clone $currentDate)->sub($oneMonthInterval),
      'current' => $currentDate,
      'next' => (clone $currentDate)->add($oneMonthInterval),
    ];
  }
}
