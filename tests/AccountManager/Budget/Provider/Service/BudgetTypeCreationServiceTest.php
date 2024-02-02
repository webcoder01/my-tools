<?php

namespace App\Tests\AccountManager\Budget\Provider\Service;

use App\AccountManager\Budget\Infrastructure\Entity\BudgetCategory;
use App\AccountManager\Budget\Infrastructure\Entity\BudgetType;
use App\AccountManager\Budget\Provider\Service\BudgetTypeCreationService;
use DG\BypassFinals;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

BypassFinals::enable();

class BudgetTypeCreationServiceTest extends KernelTestCase
{
  private EntityManagerInterface $entityManager;
  private string $budgetCategoryIdToUse;

  protected function setUp(): void
  {
    $this->entityManager = static::getContainer()->get('doctrine')->getManager();

    $budgetCategory = $this->entityManager->getRepository(BudgetCategory::class)->findBy([], null, 1)[0];
    $this->budgetCategoryIdToUse = $budgetCategory->getId();
  }

  public function testBudgetTypeIsPersistedInDatabase(): void
  {
    $budgetTypeAdditionService = new BudgetTypeCreationService($this->entityManager);
    $budgetTypeAdditionService->persistBudgetType($this->budgetCategoryIdToUse, 'type name');
    $budgetTypePersisted = $this->entityManager
      ->getRepository(BudgetType::class)
      ->findOneBy([
        'category' => $this->budgetCategoryIdToUse,
        'name' => 'type name',
      ])
    ;

    $this->assertInstanceOf(BudgetType::class, $budgetTypePersisted);
  }

  public function testReturnsIdOfNewBudgetType(): void
  {
    $budgetTypeAdditionService = new BudgetTypeCreationService($this->entityManager);
    $budgetTypeIdPersisted = $budgetTypeAdditionService
      ->persistBudgetType($this->budgetCategoryIdToUse, 'type name')
    ;

    $this->assertTrue(Uuid::isValid($budgetTypeIdPersisted));
  }
}
