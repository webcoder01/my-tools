<?php

namespace App\AccountManager\Account\Domain\UseCase;

use App\AccountManager\Account\Port\Input\AccountCreationUseCaseInterface;
use App\AccountManager\Account\Port\Output\AccountCreationServiceInterface;

class AccountCreationUseCase implements AccountCreationUseCaseInterface
{
  private AccountCreationServiceInterface $accountUpsertService;

  public function __construct(AccountCreationServiceInterface $accountUpsertService)
  {
    $this->accountUpsertService = $accountUpsertService;
  }

  public function createOne(string $userId, string $accountName, string $startingBalance): string
  {
    return $this->accountUpsertService->persistOne($userId, $accountName, $startingBalance);
  }
}
