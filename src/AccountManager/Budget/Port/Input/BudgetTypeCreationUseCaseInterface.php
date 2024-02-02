<?php

namespace App\AccountManager\Budget\Port\Input;

interface BudgetTypeCreationUseCaseInterface
{
  public function createBudgetType(string $userId, string $categoryId, string $name): string;
}
