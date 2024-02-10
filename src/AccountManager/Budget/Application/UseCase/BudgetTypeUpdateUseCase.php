<?php

namespace App\AccountManager\Budget\Application\UseCase;

use App\AccountManager\Budget\Application\Exception\ForbiddenResourceAccessException;
use App\AccountManager\Budget\Port\Input\BudgetTypeUpdateUseCaseInterface;
use App\AccountManager\Budget\Port\Output\BudgetCategoryAccessByBudgetTypeCheckerInterface;
use App\AccountManager\Budget\Port\Output\BudgetCategoryAccessByUserCheckerInterface;
use App\AccountManager\Budget\Port\Output\BudgetTypeUpdateServiceInterface;

class BudgetTypeUpdateUseCase implements BudgetTypeUpdateUseCaseInterface
{
  private BudgetCategoryAccessByBudgetTypeCheckerInterface $budgetCategoryAccessByBudgetTypeChecker;
  private BudgetCategoryAccessByUserCheckerInterface $budgetCategoryAccessByUserChecker;
  private BudgetTypeUpdateServiceInterface $budgetTypeUpdateService;

  public function __construct(
    BudgetCategoryAccessByBudgetTypeCheckerInterface $budgetCategoryAccessByBudgetTypeChecker,
    BudgetCategoryAccessByUserCheckerInterface $budgetCategoryAccessByUserChecker,
    BudgetTypeUpdateServiceInterface $budgetTypeUpdateService
  ) {
    $this->budgetCategoryAccessByBudgetTypeChecker = $budgetCategoryAccessByBudgetTypeChecker;
    $this->budgetCategoryAccessByUserChecker = $budgetCategoryAccessByUserChecker;
    $this->budgetTypeUpdateService = $budgetTypeUpdateService;
  }

  /**
   * @throws ForbiddenResourceAccessException
   */
  public function updateBudgetType(string $userId, string $budgetTypeId, string $categoryId, string $name): void
  {
    // Throw an exception if the user tries to update a budget type that is does not belong to him
    if (!$this->budgetCategoryAccessByBudgetTypeChecker->findCategoryByBudgetTypeIdAndUserId($budgetTypeId, $userId)) {
      throw new ForbiddenResourceAccessException('category');
    }

    // Throw an exception is the new category id does not exist or does not belong to the user
    if (!$this->budgetCategoryAccessByUserChecker->findCategoryByIdAndUserId($categoryId, $userId)) {
      throw new ForbiddenResourceAccessException('category');
    }

    $this->budgetTypeUpdateService->updateBudgetType($budgetTypeId, $categoryId, $name);
  }
}
