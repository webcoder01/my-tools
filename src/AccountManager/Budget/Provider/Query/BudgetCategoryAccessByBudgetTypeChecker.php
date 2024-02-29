<?php

namespace App\AccountManager\Budget\Provider\Query;

use App\AccountManager\Budget\Port\Output\BudgetCategoryAccessByBudgetTypeCheckerInterface;
use App\Shared\Infrastructure\AbstractQuery;

class BudgetCategoryAccessByBudgetTypeChecker extends AbstractQuery implements BudgetCategoryAccessByBudgetTypeCheckerInterface
{
    public function findCategoryByBudgetTypeIdAndUserId(string $budgetTypeId, string $userId): ?string
    {
        $statement = $this->entityManager->getConnection()->prepare('
      SELECT budget_type.id
      FROM budget_type
      INNER JOIN budget_category ON budget_type.category_id = budget_category.id
      WHERE budget_type.id = :budgetTypeId
      AND budget_category.user_id = :userId
    ');

        $statement->bindValue('budgetTypeId', $budgetTypeId);
        $statement->bindValue('userId', $userId);

        return $statement->executeQuery()->fetchOne();
    }
}
