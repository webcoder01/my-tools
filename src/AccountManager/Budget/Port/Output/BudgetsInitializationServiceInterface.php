<?php

namespace App\AccountManager\Budget\Port\Output;

interface BudgetsInitializationServiceInterface
{
    public function persistFirstEverBudgetsOfUser(string $userId, \DateTime $budgetsDate): void;

    public function persistBudgetsOfUserThatAreACopyOfPreviousOnes(string $userId, \DateTime $budgetsDate): void;
}
