<?php

namespace App\Tests\AccountManager\Budget\Provider\Query;

use App\AccountManager\Budget\Provider\Query\BudgetViewerQuery;
use App\Core\Security\Infrastructure\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class BudgetViewerQueryTest extends KernelTestCase
{
  private BudgetViewerQuery $budgetViewerQuery;
  private int $currentMonth;
  private int $currentYear;
  private string $userId;

  protected function setUp(): void
  {
    $kernel = self::bootKernel();

    $now = new DateTime();
    $this->currentMonth = (int) $now->format('n');
    $this->currentYear = (int) $now->format('Y');

    /** @var EntityManagerInterface $entityManager */
    $entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    $this->budgetViewerQuery = new BudgetViewerQuery($entityManager);

    $this->userId = $entityManager->getRepository(User::class)->findOneBy(['username' => 'user',])->getId();
  }

  protected function tearDown(): void
  {
    unset($this->budgetViewerQuery);
    unset($this->currentMonth);
    unset($this->currentYear);
    unset($this->userId);
  }

  public function testQueryForCategoriesHasExpectedKeysAndTypes(): void
  {
    $categories = $this->budgetViewerQuery->getCategories($this->userId, $this->currentMonth, $this->currentYear);
    $category = $categories[0];

    $this->assertCount(2, $categories);

    $this->assertTrue(Uuid::isValid($category['id']));
    $this->assertIsString($category['name']);
    $this->assertIsString($category['assigned_amount']);
    $this->assertIsString($category['available_amount']);
  }

  public function testQueryForBudgetsHasExpectedKeysAndTypes(): void
  {
    $budgets = $this->budgetViewerQuery->getBudgets($this->userId, $this->currentMonth, $this->currentYear);
    $budget = $budgets[0];

    /*
     * Expect the 3 budget types are returned,
     * event for those that have no budget
    */
    $this->assertCount(3, $budgets);

    $this->assertTrue(Uuid::isValid($budget['category_id']));
    $this->assertTrue(Uuid::isValid($budget['id']));
    $this->assertIsString($budget['name']);
    $this->assertIsString($budget['assigned_amount']);
    $this->assertIsString($budget['available_amount']);
  }

  public function testAmountsSumOfBudgetsCorrespondToCategoryAmounts(): void
  {
    $categories = $this->budgetViewerQuery->getCategories($this->userId, $this->currentMonth, $this->currentYear);
    $budgets = $this->budgetViewerQuery->getBudgets($this->userId, $this->currentMonth, $this->currentYear);

    $firstCategory = $categories[0];

    $assignedAmountOfCategory = (float) $firstCategory['assigned_amount'];
    $availableAmountOfCategory = (float) $firstCategory['available_amount'];

    $sumOfAssignedAmount = 0;
    $sumOfAvailableAmount = 0;
    $budgetsOfFirstCategory = array_filter($budgets, fn ($budget) => $budget['category_id'] === $firstCategory['id']);
    foreach ($budgetsOfFirstCategory as $budget) {
      $sumOfAssignedAmount += (float) $budget['assigned_amount'];
      $sumOfAvailableAmount += (float) $budget['available_amount'];
    }

    $this->assertEqualsWithDelta($assignedAmountOfCategory, $sumOfAssignedAmount, 0.1);
    $this->assertEqualsWithDelta($availableAmountOfCategory, $sumOfAvailableAmount, 0.1);
  }
}
