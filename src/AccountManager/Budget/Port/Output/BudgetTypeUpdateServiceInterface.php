<?php

namespace App\AccountManager\Budget\Port\Output;

interface BudgetTypeUpdateServiceInterface
{
  public function updateBudgetType(string $budgetTypeId, string $categoryId, string $name): void;
}
