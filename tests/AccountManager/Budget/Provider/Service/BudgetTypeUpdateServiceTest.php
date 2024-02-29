<?php

namespace App\Tests\AccountManager\Budget\Provider\Service;

use App\AccountManager\Budget\Provider\Entity\BudgetCategory;
use App\AccountManager\Budget\Provider\Entity\BudgetType;
use App\AccountManager\Budget\Provider\Service\BudgetTypeUpdateService;
use DG\BypassFinals;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

BypassFinals::enable();

class BudgetTypeUpdateServiceTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        unset($this->entityManager);
    }

    public function testThrowsEntityNotFoundExceptionIfBudgetTypeIsNotFoundById(): void
    {
        $this->expectException(EntityNotFoundException::class);

        $budgetTypeUpdateService = new BudgetTypeUpdateService($this->entityManager);
        $budgetTypeUpdateService->updateBudgetType(
            Uuid::v4()->toRfc4122(),
            Uuid::v4()->toRfc4122(),
            'type name updated'
        );
    }

    public function testThrowsEntityNotFoundExceptionIfCategoryIsNotFoundById(): void
    {
        $budgetTypeToUpdate = $this->entityManager
          ->getRepository(BudgetType::class)
          ->findBy([], null, 1)[0]
        ;

        $this->expectException(EntityNotFoundException::class);

        $budgetTypeUpdateService = new BudgetTypeUpdateService($this->entityManager);
        $budgetTypeUpdateService->updateBudgetType(
            $budgetTypeToUpdate->getId(),
            Uuid::v4()->toRfc4122(),
            'type name updated'
        );
    }

    public function testBudgetTypeIsUpdatedInDatabase(): void
    {
        $budgetTypeToUpdate = $this->entityManager
          ->getRepository(BudgetType::class)
          ->findBy([], null, 1)[0]
        ;
        $budgetCategory = $this->entityManager
          ->getRepository(BudgetCategory::class)
          ->findBy([], null, 1)[0]
        ;

        $budgetTypeUpdateService = new BudgetTypeUpdateService($this->entityManager);
        $budgetTypeUpdateService->updateBudgetType(
            $budgetTypeToUpdate->getId(),
            $budgetCategory->getId(),
            'type name updated'
        );

        $budgetTypeUpdated = $this->entityManager
          ->getRepository(BudgetType::class)
          ->findOneBy([
            'category' => $budgetCategory->getId(),
            'name' => 'type name updated',
          ])
        ;

        $this->assertInstanceOf(BudgetType::class, $budgetTypeUpdated);
    }
}
