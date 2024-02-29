<?php

namespace App\Tests\AccountManager\Budget\Provider\Service;

use App\AccountManager\Budget\Provider\Entity\BudgetCategory;
use App\AccountManager\Budget\Provider\Entity\BudgetType;
use App\AccountManager\Budget\Provider\Service\BudgetTypeCreationService;
use DG\BypassFinals;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
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

    public function testThrowsEntityNotFoundExceptionIfCategoryIsNotFoundById(): void
    {
        $this->expectException(EntityNotFoundException::class);

        $budgetTypeCreationService = new BudgetTypeCreationService($this->entityManager);
        $budgetTypeCreationService->persistBudgetType(
            Uuid::v4()->toRfc4122(),
            'type name'
        );
    }

    public function testBudgetTypeIsPersistedInDatabase(): void
    {
        $budgetTypeCreationService = new BudgetTypeCreationService($this->entityManager);
        $budgetTypeCreationService->persistBudgetType($this->budgetCategoryIdToUse, 'type name');
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
        $budgetTypeCreationService = new BudgetTypeCreationService($this->entityManager);
        $budgetTypeIdPersisted = $budgetTypeCreationService
          ->persistBudgetType($this->budgetCategoryIdToUse, 'type name')
        ;

        $this->assertTrue(Uuid::isValid($budgetTypeIdPersisted));
    }
}
