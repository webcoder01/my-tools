<?php

namespace App\Tests\AccountManager\Budget\Application\UseCase;

use App\AccountManager\Budget\Application\Exception\BudgetsAlreadyInitializedException;
use App\AccountManager\Budget\Application\Exception\IncorrectBudgetDatesException;
use App\AccountManager\Budget\Application\UseCase\BudgetsInitializationUseCase;
use App\AccountManager\Budget\Infrastructure\Entity\Budget;
use App\AccountManager\Budget\Infrastructure\Entity\BudgetCategory;
use App\AccountManager\Budget\Infrastructure\Entity\BudgetType;
use App\AccountManager\Budget\Port\Output\BudgetsInitializationQueryInterface;
use App\AccountManager\Budget\Provider\Query\BudgetsInitializationQuery;
use App\AccountManager\Budget\Provider\Service\BudgetsInitializationService;
use App\Core\Security\Infrastructure\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BudgetsInitializationUseCaseTest extends KernelTestCase
{
  use ProphecyTrait;

  private BudgetsInitializationUseCase $budgetsInitializationUseCase;
  private EntityManagerInterface $entityManager;
  private ObjectProphecy|BudgetsInitializationQueryInterface $lastBudgetsInitializedByUserQuery;
  private User $user;

  protected function setUp(): void
  {
    self::bootKernel();
    $container = static::getContainer();

    $this->entityManager = $container->get('doctrine')->getManager();
    $this->user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);

    //$this->lastBudgetsInitializedByUserQuery = $this->prophesize(BudgetsInitializationQueryInterface::class);
    $lastBudgetsInitializedByUserQuery = new BudgetsInitializationQuery($this->entityManager);
    $budgetsInitializationService = new BudgetsInitializationService($this->entityManager);

    $this->budgetsInitializationUseCase = new BudgetsInitializationUseCase(
      $lastBudgetsInitializedByUserQuery,
      $budgetsInitializationService
    );
  }

  protected function tearDown(): void
  {
    unset($this->user);
    unset($this->budgetsInitializationUseCase);
    parent::tearDown();
  }

  public function testThrowsIncorrectBudgetDatesExceptionIfMonthAndYearAreNotTheNext(): void
  {
    $date = new DateTime('+2 months');
    $month = (int) $date->format('n');
    $year = (int) $date->format('Y');

    $this->expectException(IncorrectBudgetDatesException::class);

    $this->budgetsInitializationUseCase->initiate($this->user->getId(), $month, $year);
  }

  public function testThrowsBudgetsAlreadyInitializedExceptionIfInitializationHasBeenDone(): void
  {
    $nextMonthDate = new DateTime('next month');
    $nextMonth = (int) $nextMonthDate->format('n');
    $nextYear = (int) $nextMonthDate->format('Y');

    $this->budgetsInitializationUseCase->initiate($this->user->getId(), $nextMonth, $nextYear);

    $this->expectException(BudgetsAlreadyInitializedException::class);

    $this->budgetsInitializationUseCase->initiate($this->user->getId(), $nextMonth, $nextYear);
  }

  public function testThereAreAsManyInitializedBudgetsAsBudgetTypes(): void
  {
    $nextMonthDate = new DateTime('next month');
    $nextMonth = (int) $nextMonthDate->format('n');
    $nextYear = (int) $nextMonthDate->format('Y');

    $budgetCategories = $this->entityManager
      ->getRepository(BudgetCategory::class)
      ->findBy(['user' => $this->user])
    ;
    $budgetTypes = $this->entityManager
      ->getRepository(BudgetType::class)
      ->findBy(['category' => $budgetCategories])
    ;

    $this->budgetsInitializationUseCase->initiate($this->user->getId(), $nextMonth, $nextYear);

    $budgetsCount = $this->entityManager
      ->getRepository(Budget::class)
      ->count([
        'type' => $budgetTypes,
        'month' => $nextMonth,
        'year' => $nextYear,
      ])
    ;

    $this->assertSame(count($budgetTypes), $budgetsCount);
  }

  public function testAllAmountsOfInitializedBudgetsAreZeroIfThereAreNoPreviousBudgets(): void
  {
    $deletePreviousBudgetsStatement = $this->entityManager->getConnection()->prepare('
      DELETE budget
      FROM budget
      INNER JOIN budget_type ON budget.type_id = budget_type.id
      INNER JOIN budget_category AS category ON budget_type.category_id = category.id
      WHERE category.user_id = :userId
    ');
    $deletePreviousBudgetsStatement->bindValue('userId', $this->user->getId());
    $deletePreviousBudgetsStatement->executeStatement();

    $currentDate = new DateTime();
    $currentMonth = $currentDate->format('n');
    $currentYear = $currentDate->format('Y');

    $this->budgetsInitializationUseCase->initiate($this->user->getId(), $currentMonth, $currentYear);

    $sumOfInitializedBudgetsStatement = $this->entityManager->getConnection()->prepare('
      SELECT
        SUM(budget.assigned_amount) AS assigned_amount,
        SUM(budget.available_amount) AS available_amount
      FROM budget
      INNER JOIN budget_type ON budget.type_id = budget_type.id
      INNER JOIN budget_category AS category ON budget_type.category_id = category.id
      WHERE budget.month = :month
      AND budget.year = :year
      AND category.user_id = :userId
    ');
    $sumOfInitializedBudgetsStatement->bindValue('month', $currentMonth);
    $sumOfInitializedBudgetsStatement->bindValue('year', $currentYear);
    $sumOfInitializedBudgetsStatement->bindValue('userId', $this->user->getId());
    $sumOfInitializedBudgets = $sumOfInitializedBudgetsStatement->executeQuery()->fetchAssociative();

    $this->assertSame('0.00', $sumOfInitializedBudgets['assigned_amount']);
    $this->assertSame('0.00', $sumOfInitializedBudgets['available_amount']);
  }

  public function testSumOfInitializedBudgetsIsTheSameAsTheSumOfPreviousBudgets(): void
  {
    $currentDate = new DateTime();
    $currentMonth = $currentDate->format('n');
    $currentYear = $currentDate->format('Y');

    $sumOfCurrentBudgetsStatement = $this->entityManager->getConnection()->prepare('
      SELECT
        SUM(budget.assigned_amount) AS assigned_amount,
        SUM(budget.available_amount) AS available_amount
      FROM budget
      INNER JOIN budget_type ON budget.type_id = budget_type.id
      INNER JOIN budget_category AS category ON budget_type.category_id = category.id
      WHERE budget.month = :month
      AND budget.year = :year
      AND category.user_id = :userId
    ');
    $sumOfCurrentBudgetsStatement->bindValue('month', $currentMonth);
    $sumOfCurrentBudgetsStatement->bindValue('year', $currentYear);
    $sumOfCurrentBudgetsStatement->bindValue('userId', $this->user->getId());
    $sumOfCurrentBudgets = $sumOfCurrentBudgetsStatement->executeQuery()->fetchAssociative();

    $nextMonthDate = new DateTime('next month');
    $nextMonth = (int) $nextMonthDate->format('n');
    $nextYear = (int) $nextMonthDate->format('Y');
    $this->budgetsInitializationUseCase->initiate($this->user->getId(), $nextMonth, $nextYear);

    $sumOfInitializedBudgetsStatement = $this->entityManager->getConnection()->prepare('
      SELECT
        SUM(budget.assigned_amount) AS assigned_amount,
        SUM(budget.available_amount) AS available_amount
      FROM budget
      INNER JOIN budget_type ON budget.type_id = budget_type.id
      INNER JOIN budget_category AS category ON budget_type.category_id = category.id
      WHERE budget.month = :month
      AND budget.year = :year
      AND category.user_id = :userId
    ');
    $sumOfInitializedBudgetsStatement->bindValue('month', $nextMonth);
    $sumOfInitializedBudgetsStatement->bindValue('year', $nextYear);
    $sumOfInitializedBudgetsStatement->bindValue('userId', $this->user->getId());
    $sumOfInitializedBudgets = $sumOfInitializedBudgetsStatement->executeQuery()->fetchAssociative();

    $this->assertSame($sumOfCurrentBudgets['assigned_amount'], $sumOfInitializedBudgets['assigned_amount']);
    $this->assertSame($sumOfCurrentBudgets['available_amount'], $sumOfInitializedBudgets['available_amount']);
  }
}
