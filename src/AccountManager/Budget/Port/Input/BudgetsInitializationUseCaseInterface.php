<?php

namespace App\AccountManager\Budget\Port\Input;

interface BudgetsInitializationUseCaseInterface
{
  public function initiate(): void;
}
