<?php

namespace App\AccountManager\Budget\Port\Output;

use DateTime;

interface BudgetsInitializationQueryInterface
{
  public function findDateFromLastBudgetsInitializedByUser(string $userId): ?DateTime;

  public function getBudgetsCountOfUserByMonthAndYear(string $userId, int $month, int $year): int;
}
