<?php

namespace App\AccountManager\Budget\Infrastructure\Query;

use App\Shared\Infrastructure\AbstractQuery;

final class BudgetViewerQuery extends AbstractQuery implements BudgetViewerQueryInterface
{
  /**
   * @return array{
   *   array{
   *     id: string,
   *     name: string,
   *     assigned_amount: string,
   *     available_amount: string,
   *   }
   * }
   */
  public function getCategories(string $userId, int $month, int $year): array
  {
    $statement = $this->entityManager->getConnection()->prepare("
      SELECT
        category.id AS id,
        category.name AS name,
        SUM(budget.assigned_amount) AS assigned_amount,
        SUM(budget.available_amount) AS available_amount
      FROM budget_category AS category
      INNER JOIN user ON user.id = category.user_id
      LEFT JOIN budget_type AS type ON type.category_id = category.id
      LEFT JOIN budget ON budget.type_id = type.id AND budget.month = :month AND budget.year = :year
      WHERE user.id = :userId
      GROUP BY category.id, category.name
      ORDER BY category.name ASC
    ");

    $statement->bindValue('userId', $userId);
    $statement->bindValue('month', $month);
    $statement->bindValue('year', $year);

    return $statement->executeQuery()->fetchAllAssociative();
  }

  /**
   * @return array{
   *   array{
   *     category_id: string,
   *     name: string,
   *     assigned_amount: string,
   *     available_amount: string,
   *   }
   * }
   */
  public function getBudgets(string $userId, int $month, int $year): array
  {
    $statement = $this->entityManager->getConnection()->prepare("
      SELECT
        category.id AS category_id,
        type.name AS name,
        budget.assigned_amount,
        budget.available_amount
      FROM budget_category AS category
      INNER JOIN user ON user.id = category.user_id
      LEFT JOIN budget_type AS type ON type.category_id = category.id
      LEFT JOIN budget ON budget.type_id = type.id AND budget.month = :month AND budget.year = :year
      WHERE user.id = :userId
      ORDER BY category.id ASC, type.name ASC
    ");

    $statement->bindValue('userId', $userId);
    $statement->bindValue('month', $month);
    $statement->bindValue('year', $year);

    return $statement->executeQuery()->fetchAllAssociative();
  }
}
