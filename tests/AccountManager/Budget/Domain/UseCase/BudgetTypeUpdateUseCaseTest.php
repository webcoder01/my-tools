<?php

namespace App\Tests\AccountManager\Budget\Domain\UseCase;

use App\AccountManager\Budget\Domain\Exception\ForbiddenResourceAccessException;
use App\AccountManager\Budget\Domain\UseCase\BudgetTypeUpdateUseCase;
use App\AccountManager\Budget\Infrastructure\Entity\BudgetCategory;
use App\AccountManager\Budget\Infrastructure\Entity\BudgetType;
use App\AccountManager\Budget\Provider\Query\BudgetCategoryAccessByBudgetTypeChecker;
use App\AccountManager\Budget\Provider\Query\BudgetCategoryAccessByUserChecker;
use App\AccountManager\Budget\Provider\Service\BudgetTypeUpdateService;
use App\Core\Security\Infrastructure\Entity\User;
use DG\BypassFinals;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

BypassFinals::enable();

class BudgetTypeUpdateUseCaseTest extends KernelTestCase
{
  private BudgetTypeUpdateUseCase $budgetTypeUpdateUseCase;
  private EntityManagerInterface $entityManager;
  private string $userId;

  protected function setUp(): void
  {
    self::bootKernel();
    $container = static::getContainer();
    $this->entityManager = $container->get('doctrine')->getManager();
    $budgetCategoryByBudgetTypeIdChecker = new BudgetCategoryAccessByBudgetTypeChecker($this->entityManager);
    $budgetCategoryByUserIdChecker = new BudgetCategoryAccessByUserChecker($this->entityManager);

    $budgetTypeUpdateService = $container->get(BudgetTypeUpdateService::class);
    $this->budgetTypeUpdateUseCase = new BudgetTypeUpdateUseCase(
      $budgetCategoryByBudgetTypeIdChecker,
      $budgetCategoryByUserIdChecker,
      $budgetTypeUpdateService
    );

    $this->userId = $this->entityManager
      ->getRepository(User::class)
      ->findOneBy(['username' => 'user'])->getId()
    ;
  }

  protected function tearDown(): void
  {
    unset($this->budgetTypeUpdateUseCase);
    unset($this->entityManager);
    unset($this->userId);
  }

  public function testThrowsForbiddenResourceAccessExceptionIfUserDoesNotOwnBudgetCategory(): void
  {
    $this->expectException(ForbiddenResourceAccessException::class);

    $this->budgetTypeUpdateUseCase->updateBudgetType(
      $this->userId,
      UUid::v4()->toRfc4122(),
      UUid::v4()->toRfc4122(),
      'type name updated'
    );
  }

  public function testThrowsForbiddenResourceAccessExceptionIfNewCategoryDoesNotExist(): void
  {
    $this->expectException(ForbiddenResourceAccessException::class);

    $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);
    $budgetCategoryOfUser = $this->entityManager
      ->getRepository(BudgetCategory::class)
      ->findOneBy(['user' => $user->getId()])
    ;
    $budgetTypeToEdit = $this->entityManager
      ->getRepository(BudgetType::class)
      ->findOneBy(['category' => $budgetCategoryOfUser->getId()])
    ;

    $this->budgetTypeUpdateUseCase->updateBudgetType(
      $this->userId,
      $budgetTypeToEdit->getId(),
      UUid::v4()->toRfc4122(),
      'type name updated'
    );
  }

  public function testBudgetTypeIsUpdated(): void
  {
    $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);
    $budgetCategoryOfUser = $this->entityManager
      ->getRepository(BudgetCategory::class)
      ->findOneBy(['user' => $user->getId()])
    ;
    $budgetTypeToEdit = $this->entityManager
      ->getRepository(BudgetType::class)
      ->findOneBy(['category' => $budgetCategoryOfUser->getId()])
    ;

    $this->budgetTypeUpdateUseCase->updateBudgetType(
      $this->userId,
      $budgetTypeToEdit->getId(),
      $budgetCategoryOfUser->getId(),
      'type name updated'
    );

    $budgetTypeUpdated = $this->entityManager
      ->getRepository(BudgetType::class)
      ->findOneBy(['name' => 'type name updated'])
    ;

    $this->assertSame($budgetTypeToEdit->getId(), $budgetTypeUpdated->getId());
  }
}
