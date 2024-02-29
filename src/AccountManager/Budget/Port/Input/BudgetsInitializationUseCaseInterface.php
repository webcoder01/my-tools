<?php

namespace App\AccountManager\Budget\Port\Input;

use App\AccountManager\Budget\Domain\Exception\IncorrectBudgetDatesException;

interface BudgetsInitializationUseCaseInterface
{
    /**
     * @throws IncorrectBudgetDatesException
     */
    public function initiate(string $userId, int $month, int $year): void;
}
