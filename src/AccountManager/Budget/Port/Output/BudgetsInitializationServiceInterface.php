<?php

namespace App\AccountManager\Budget\Port\Output;

use DateTime;

interface BudgetsInitializationServiceInterface
{
  public function persistFirstEverBudgetsOfUser(string $userId, DateTime $budgetsDate): void;

  public function persistBudgetsOfUserThatAreACopyOfPreviousOnes(string $userId, DateTime $budgetsDate): void;
}
