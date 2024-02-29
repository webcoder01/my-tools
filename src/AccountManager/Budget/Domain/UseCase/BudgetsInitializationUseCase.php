<?php

namespace App\AccountManager\Budget\Domain\UseCase;

use App\AccountManager\Budget\Domain\Exception\BudgetsAlreadyInitializedException;
use App\AccountManager\Budget\Domain\Exception\IncorrectBudgetDatesException;
use App\AccountManager\Budget\Port\Input\BudgetsInitializationUseCaseInterface;
use App\AccountManager\Budget\Port\Output\BudgetsInitializationQueryInterface;
use App\AccountManager\Budget\Port\Output\BudgetsInitializationServiceInterface;

class BudgetsInitializationUseCase implements BudgetsInitializationUseCaseInterface
{
    private BudgetsInitializationQueryInterface $budgetsInitializationQuery;
    private BudgetsInitializationServiceInterface $budgetsInitializationService;

    private string $userId;
    private int $month;
    private int $year;

    public function __construct(
        BudgetsInitializationQueryInterface $budgetsInitializationQuery,
        BudgetsInitializationServiceInterface $budgetsInitializationService
    ) {
        $this->budgetsInitializationQuery = $budgetsInitializationQuery;
        $this->budgetsInitializationService = $budgetsInitializationService;
    }

    public function initiate(string $userId, int $month, int $year): void
    {
        $this->userId = $userId;
        $this->month = $month;
        $this->year = $year;

        $dateFromRequest = new \DateTime(sprintf('%d-%d-01', $year, $month));
        $dateOfLastBudgetsFromDb = $this->budgetsInitializationQuery
          ->findDateFromLastBudgetsInitializedByUser($userId)
        ;

        if (null === $dateOfLastBudgetsFromDb) {
            $this->initializeFirstEverBudgets();

            return;
        }

        $this->checkDateFromRequestIsCorrect($dateFromRequest, $dateOfLastBudgetsFromDb);

        $this->budgetsInitializationService->persistBudgetsOfUserThatAreACopyOfPreviousOnes($userId, $dateFromRequest);
    }

    private function initializeFirstEverBudgets(): void
    {
        $date = new \DateTime(sprintf('%d-%d-01', $this->year, $this->month));
        $this->budgetsInitializationService->persistFirstEverBudgetsOfUser($this->userId, $date);
    }

    /**
     * @throws IncorrectBudgetDatesException
     * @throws BudgetsAlreadyInitializedException
     */
    private function checkDateFromRequestIsCorrect(\DateTime $dateFromRequest, \DateTime $dateOfLastBudgetsFromDb): void
    {
        $interval = $this->getMonthIntervalSinceDateOfLastBudgets($dateOfLastBudgetsFromDb, $dateFromRequest);
        if ($this->isDateFromRequestAnteriorToDateOfLastBudgets($interval)) {
            throw new IncorrectBudgetDatesException();
        }
        if ($this->isDateFromRequestTooFarFromLastDateOfBudgets($interval)) {
            throw new IncorrectBudgetDatesException();
        }
        if ($this->isDateFromRequestTheSameAsLastDateOfBudgets($interval)) {
            throw new BudgetsAlreadyInitializedException();
        }
    }

    private function getMonthIntervalSinceDateOfLastBudgets(
        \DateTime $lastInitializedBudgetsDate,
        \DateTime $dateFromRequest
    ): int {
        return (int) $lastInitializedBudgetsDate->diff($dateFromRequest)->format('%r%m');
    }

    private function isDateFromRequestAnteriorToDateOfLastBudgets(int $interval): bool
    {
        return $interval < 0;
    }

    private function isDateFromRequestTooFarFromLastDateOfBudgets(int $interval): bool
    {
        return $interval > 1;
    }

    private function isDateFromRequestTheSameAsLastDateOfBudgets(int $interval): bool
    {
        return 0 === $interval;
    }
}
