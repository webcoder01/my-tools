<?php

namespace App\AccountManager\Budget\Port\Output;

interface BudgetCategoryAccessByBudgetTypeCheckerInterface
{
  public function findCategoryByBudgetTypeIdAndUserId(string $budgetTypeId, string $userId): ?string;
}
