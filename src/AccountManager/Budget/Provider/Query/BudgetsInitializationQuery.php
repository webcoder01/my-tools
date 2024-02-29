<?php

namespace App\AccountManager\Budget\Provider\Query;

use App\AccountManager\Budget\Port\Output\BudgetsInitializationQueryInterface;
use App\Shared\Infrastructure\AbstractQuery;

class BudgetsInitializationQuery extends AbstractQuery implements BudgetsInitializationQueryInterface
{
    public function findDateFromLastBudgetsInitializedByUser(string $userId): ?\DateTime
    {
        $statement = $this->entityManager->getConnection()->prepare('
      SELECT MAX(budget.month) AS month, MAX(budget.year) AS year
      FROM budget
      INNER JOIN budget_type ON budget.type_id = budget_type.id
      INNER JOIN budget_category ON budget_type.category_id = budget_category.id
      WHERE budget_category.user_id = :userId
    ');

        $statement->bindValue('userId', $userId);
        $monthAndYear = $statement->executeQuery()->fetchAssociative();

        if (null === $monthAndYear['month'] && null === $monthAndYear['year']) {
            return null;
        }

        return new \DateTime(sprintf('%d-%d-01', $monthAndYear['year'], $monthAndYear['month']));
    }

    public function getBudgetsCountOfUserByMonthAndYear(string $userId, int $month, int $year): int
    {
        $statement = $this->entityManager->getConnection()->prepare('
      SELECT budget.*
      FROM budget
      INNER JOIN budget_type ON budget.type_id = budget_type.id
      INNER JOIN budget_category ON budget_type.category_id = budget_category.id
      WHERE budget_category.user_id = :userId
      AND budget.month = :month
      AND budget.year = :year
    ');

        $statement->bindValue('userId', $userId);
        $statement->bindValue('month', $month);
        $statement->bindValue('year', $year);

        return $statement->executeQuery()->rowCount();
    }
}
