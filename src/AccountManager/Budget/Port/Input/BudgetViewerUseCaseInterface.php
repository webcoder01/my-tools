<?php

namespace App\AccountManager\Budget\Port\Input;

interface BudgetViewerUseCaseInterface
{
    public function getViewOfMonth(string $userId, int $month, int $year): array;

    public function getNavigationDates(\DateTime $currentDate): array;
}
