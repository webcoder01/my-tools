<?php

namespace App\AccountManager\Budget\Application\UseCase;

interface BudgetViewerInterface
{
  public function getViewOfMonth(string $userId, int $month, int $year): array;
}
