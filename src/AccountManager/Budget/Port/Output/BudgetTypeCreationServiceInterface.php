<?php

namespace App\AccountManager\Budget\Port\Output;

interface BudgetTypeCreationServiceInterface
{
    public function persistBudgetType(string $categoryId, string $name): string;
}
