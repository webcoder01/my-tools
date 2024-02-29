<?php

namespace App\AccountManager\Budget\Provider\Query;

use App\AccountManager\Budget\Port\Output\BudgetCategoryAccessByUserCheckerInterface;
use App\Shared\Infrastructure\AbstractQuery;

class BudgetCategoryAccessByUserChecker extends AbstractQuery implements BudgetCategoryAccessByUserCheckerInterface
{
    public function findCategoryByIdAndUserId(string $categoryId, string $userId): ?string
    {
        $statement = $this->entityManager->getConnection()->prepare('
      SELECT id
      FROM budget_category
      WHERE id = :categoryId
      AND user_id = :userId
    ');

        $statement->bindValue('categoryId', $categoryId);
        $statement->bindValue('userId', $userId);

        return $statement->executeQuery()->fetchOne();
    }
}
