<?php

namespace App\AccountManager\Budget\Application\UseCase;

use DateTime;

interface BudgetViewerInterface
{
  public function getViewOfMonth(string $userId, int $month, int $year): array;

  public function getNavigationDates(DateTime $currentDate): array;
}
