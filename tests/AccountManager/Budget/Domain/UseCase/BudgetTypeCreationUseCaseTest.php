<?php

namespace App\Tests\AccountManager\Budget\Domain\UseCase;

use App\AccountManager\Budget\Domain\Exception\ForbiddenResourceAccessException;
use App\AccountManager\Budget\Domain\UseCase\BudgetTypeCreationUseCase;
use App\AccountManager\Budget\Provider\Entity\BudgetCategory;
use App\AccountManager\Budget\Provider\Query\BudgetCategoryAccessByUserChecker;
use App\AccountManager\Budget\Provider\Service\BudgetTypeCreationService;
use App\Core\Security\Provider\Entity\User;
use DG\BypassFinals;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

BypassFinals::enable();

class BudgetTypeCreationUseCaseTest extends KernelTestCase
{
    private BudgetTypeCreationUseCase $budgetTypeCreationUseCase;
    private EntityManagerInterface $entityManager;
    private string $userId;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();
        $budgetCategoryByUserIdFinder = new BudgetCategoryAccessByUserChecker($this->entityManager);

        $budgetTypeCreationService = $container->get(BudgetTypeCreationService::class);
        $this->budgetTypeCreationUseCase = new BudgetTypeCreationUseCase(
            $budgetCategoryByUserIdFinder,
            $budgetTypeCreationService
        );

        $this->userId = $this->entityManager
          ->getRepository(User::class)
          ->findOneBy(['username' => 'user'])->getId()
        ;
    }

    protected function tearDown(): void
    {
        unset($this->budgetTypeAdditionUseCase);
        unset($this->budgetTypeAdditionService);
        unset($this->entityManager);
        unset($this->userId);
    }

    public function testThrowsForbiddenResourceAccessExceptionIfUserDoesNotOwnBudgetCategory(): void
    {
        $this->expectException(ForbiddenResourceAccessException::class);

        $randomBudgetCategory = Uuid::v4()->toRfc4122();
        $this->budgetTypeCreationUseCase->createBudgetType($this->userId, $randomBudgetCategory, 'type name');
    }

    public function testCallsServiceThatPersistBudgetTypeAndReturnsItsId(): void
    {
        $budgetCategoryId = $this->entityManager
          ->getRepository(BudgetCategory::class)
          ->findOneBy(['user' => $this->userId])
          ->getId()
        ;
        $budgetTypeIdPersisted = $this->budgetTypeCreationUseCase
          ->createBudgetType($this->userId, $budgetCategoryId, 'type name')
        ;

        $this->assertTrue(Uuid::isValid($budgetTypeIdPersisted));
    }
}
