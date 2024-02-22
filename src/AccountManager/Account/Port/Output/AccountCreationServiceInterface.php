<?php

namespace App\AccountManager\Account\Port\Output;

interface AccountCreationServiceInterface
{
  public function persistOne(string $userId, string $accountName, string $startingBalance): string;
}
