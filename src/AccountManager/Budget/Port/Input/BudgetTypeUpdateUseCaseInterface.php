<?php

namespace App\AccountManager\Budget\Port\Input;

use App\AccountManager\Budget\Domain\Exception\ForbiddenResourceAccessException;

interface BudgetTypeUpdateUseCaseInterface
{
    /**
     * @throws ForbiddenResourceAccessException
     */
    public function updateBudgetType(string $userId, string $budgetTypeId, string $categoryId, string $name): void;
}
