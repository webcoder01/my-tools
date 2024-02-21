<?php

namespace App\AccountManager\Budget\Domain\UseCase;

use App\AccountManager\Budget\Domain\Exception\ForbiddenResourceAccessException;
use App\AccountManager\Budget\Port\Input\BudgetTypeCreationUseCaseInterface;
use App\AccountManager\Budget\Port\Output\BudgetCategoryAccessByUserCheckerInterface;
use App\AccountManager\Budget\Port\Output\BudgetTypeCreationServiceInterface;

final class BudgetTypeCreationUseCase implements BudgetTypeCreationUseCaseInterface
{
  private BudgetCategoryAccessByUserCheckerInterface $budgetCategoryAccessByUserChecker;
  private BudgetTypeCreationServiceInterface $budgetTypeCreationService;

  public function __construct(
    BudgetCategoryAccessByUserCheckerInterface $budgetCategoryAccessByUserChecker,
    BudgetTypeCreationServiceInterface $budgetTypeAdditionService
  ) {
    $this->budgetCategoryAccessByUserChecker = $budgetCategoryAccessByUserChecker;
    $this->budgetTypeCreationService = $budgetTypeAdditionService;
  }

  /**
   * @throws ForbiddenResourceAccessException
   */
  public function createBudgetType(string $userId, string $categoryId, string $name): string
  {
    if (!$this->budgetCategoryAccessByUserChecker->findCategoryByIdAndUserId($categoryId, $userId)) {
      throw new ForbiddenResourceAccessException('category');
    }

    return $this->budgetTypeCreationService->persistBudgetType($categoryId, $name);
  }
}
