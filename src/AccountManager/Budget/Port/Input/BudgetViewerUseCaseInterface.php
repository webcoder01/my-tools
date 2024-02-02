<?php

namespace App\AccountManager\Budget\Port\Input;

use DateTime;

interface BudgetViewerUseCaseInterface
{
  public function getViewOfMonth(string $userId, int $month, int $year): array;

  public function getNavigationDates(DateTime $currentDate): array;
}
