<?php

namespace App\AccountManager\Budget\Port\Input;

use App\AccountManager\Budget\Domain\Exception\ForbiddenResourceAccessException;

interface BudgetTypeCreationUseCaseInterface
{
  /**
   * @throws ForbiddenResourceAccessException
   */
  public function createBudgetType(string $userId, string $categoryId, string $name): string;
}
