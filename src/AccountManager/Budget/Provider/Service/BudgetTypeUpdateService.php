<?php

namespace App\AccountManager\Budget\Provider\Service;

use App\AccountManager\Budget\Port\Output\BudgetTypeUpdateServiceInterface;
use App\AccountManager\Budget\Provider\Entity\BudgetCategory;
use App\AccountManager\Budget\Provider\Entity\BudgetType;
use App\Shared\Provider\AbstractService;
use Doctrine\ORM\EntityNotFoundException;

class BudgetTypeUpdateService extends AbstractService implements BudgetTypeUpdateServiceInterface
{
  /**
   * @throws EntityNotFoundException
   */
  public function updateBudgetType(string $budgetTypeId, string $categoryId, string $name): void
  {
    $budgetTypeToUpdate = $this->entityManager->getRepository(BudgetType::class)->find($budgetTypeId);
    if ($budgetTypeToUpdate === null) {
      throw new EntityNotFoundException();
    }

    $budgetCategory = $this->entityManager->getRepository(BudgetCategory::class)->find($categoryId);
    if ($budgetCategory === null) {
      throw new EntityNotFoundException();
    }

    $budgetTypeToUpdate->setCategory($budgetCategory);
    $budgetTypeToUpdate->setName($name);

    $this->entityManager->flush();
    $this->entityManager->clear();
  }
}
