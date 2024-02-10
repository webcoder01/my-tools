<?php

namespace App\AccountManager\Budget\Port\Input;

use App\AccountManager\Budget\Application\Exception\ForbiddenResourceAccessException;

interface BudgetTypeUpdateUseCaseInterface
{
  /**
   * @throws ForbiddenResourceAccessException
   */
  public function updateBudgetType(string $userId, string $budgetTypeId, string $categoryId, string $name): void;
}
