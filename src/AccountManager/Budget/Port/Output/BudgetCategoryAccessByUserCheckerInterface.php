<?php

namespace App\AccountManager\Budget\Port\Output;

interface BudgetCategoryAccessByUserCheckerInterface
{
  public function findCategoryByIdAndUserId(string $categoryId, string $userId): ?string;
}
