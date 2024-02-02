<?php

namespace App\AccountManager\Budget\Provider\Service;

use App\AccountManager\Budget\Infrastructure\Entity\BudgetCategory;
use App\AccountManager\Budget\Infrastructure\Entity\BudgetType;
use App\AccountManager\Budget\Port\Output\BudgetTypeCreationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

final class BudgetTypeCreationService implements BudgetTypeCreationServiceInterface
{
  private EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function persistBudgetType(string $categoryId, string $name): string
  {
    $budgetCategory = $this->entityManager->getRepository(BudgetCategory::class)->find($categoryId);

    $budgetType = new BudgetType();
    $budgetType->setName($name);
    $budgetType->setCategory($budgetCategory);

    $this->entityManager->persist($budgetType);
    $this->entityManager->flush();

    return $budgetType->getId();
  }
}
