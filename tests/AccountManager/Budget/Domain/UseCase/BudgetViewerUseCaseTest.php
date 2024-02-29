<?php

namespace App\Tests\AccountManager\Budget\Domain\UseCase;

use App\AccountManager\Budget\Domain\UseCase\BudgetViewerUseCase;
use App\AccountManager\Budget\Port\Output\BudgetViewerQueryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Uid\Uuid;

class BudgetViewerUseCaseTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|BudgetViewerQueryInterface $budgetViewerQuery;
    private BudgetViewerUseCase $budgetViewerUseCase;
    private string $userId;
    private int $month = 1;
    private int $year = 1;

    protected function setUp(): void
    {
        $this->budgetViewerQuery = $this->prophesize(BudgetViewerQueryInterface::class);
        $this->budgetViewerUseCase = new BudgetViewerUseCase($this->budgetViewerQuery->reveal());
        $this->userId = Uuid::v4()->toRfc4122();
    }

    protected function tearDown(): void
    {
        unset($this->budgetViewerQuery);
        unset($this->budgetViewerUseCase);
        unset($this->userId);
        unset($this->month);
        unset($this->year);
    }

    public function testReturnsAnEmptyArrayWhenThereIsNoData(): void
    {
        $this->budgetViewerQuery->getCategories($this->userId, $this->month, $this->year)->willReturn([]);
        $this->budgetViewerQuery->getBudgets($this->userId, $this->month, $this->year)->willReturn([]);
        $view = $this->budgetViewerUseCase->getViewOfMonth($this->userId, $this->month, $this->year);

        $this->assertIsArray($view);
        $this->assertCount(0, $view);
    }

    public function testReturnsCategoryWithExpectedRootData(): void
    {
        $categoryId = Uuid::v4()->toRfc4122();
        $this->budgetViewerQuery->getCategories($this->userId, $this->month, $this->year)->willReturn([
          [
            'id' => $categoryId,
            'name' => 'category',
            'assigned_amount' => '150.00',
            'available_amount' => '150.00',
          ],
        ]);
        $this->budgetViewerQuery->getBudgets($this->userId, $this->month, $this->year)->willReturn([
          [
            'category_id' => $categoryId,
            'name' => 'budget 1',
            'assigned_amount' => '100.00',
            'available_amount' => '100.00',
          ],
          [
            'category_id' => $categoryId,
            'name' => 'budget 2',
            'assigned_amount' => '50.00',
            'available_amount' => '50.00',
          ],
        ]);
        $view = $this->budgetViewerUseCase->getViewOfMonth($this->userId, $this->month, $this->year);
        $category = $view[0];

        $this->assertIsArray($category);
        $this->assertSame($categoryId, $category['id']);
        $this->assertSame('category', $category['name']);
        $this->assertSame('150.00', $category['assigned_amount']);
        $this->assertSame('150.00', $category['available_amount']);
    }

    public function testReturnsCategoryWithExpectedBudgets(): void
    {
        $categoryId = Uuid::v4()->toRfc4122();
        $this->budgetViewerQuery->getCategories($this->userId, $this->month, $this->year)->willReturn([
          [
            'id' => $categoryId,
            'name' => 'category',
            'assigned_amount' => '150.00',
            'available_amount' => '150.00',
          ],
        ]);
        $this->budgetViewerQuery->getBudgets($this->userId, $this->month, $this->year)->willReturn([
          [
            'category_id' => $categoryId,
            'name' => 'budget 1',
            'assigned_amount' => '100.00',
            'available_amount' => '100.00',
          ],
          [
            'category_id' => $categoryId,
            'name' => 'budget 2',
            'assigned_amount' => '50.00',
            'available_amount' => '50.00',
          ],
        ]);
        $view = $this->budgetViewerUseCase->getViewOfMonth($this->userId, $this->month, $this->year);
        $budgetsOfCategory = $view[0]['budgets'];

        $this->assertCount(2, $budgetsOfCategory);

        $firstBudget = $budgetsOfCategory[0];
        $this->assertSame('budget 1', $firstBudget['name']);
        $this->assertSame('100.00', $firstBudget['assigned_amount']);
        $this->assertSame('100.00', $firstBudget['available_amount']);

        $lastBudget = $budgetsOfCategory[1];
        $this->assertSame('budget 2', $lastBudget['name']);
        $this->assertSame('50.00', $lastBudget['assigned_amount']);
        $this->assertSame('50.00', $lastBudget['available_amount']);
    }

    public function testReturnsNavigationDatesCorrectly(): void
    {
        $now = new \DateTime();
        $navigationDates = $this->budgetViewerUseCase->getNavigationDates($now);

        $previousDateInterval = $now->diff($navigationDates['previous']);
        $nextDateInterval = $now->diff($navigationDates['next']);

        $this->assertSame('-1', $previousDateInterval->format('%r%m'));
        $this->assertSame('1', $nextDateInterval->format('%r%m'));
    }
}
