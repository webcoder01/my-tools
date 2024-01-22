<?php

namespace App\AccountManager\Budget\Infrastructure\Query;

interface BudgetViewerQueryInterface
{
  public function getCategories(string $userId, int $month, int $year): array;

  public function getBudgets(string $userId, int $month, int $year): array;
}
