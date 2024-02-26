<?php

namespace App\AccountManager\Account\Port\Output;

interface AccountByUserFinderServiceInterface
{
  public function isAccountOwnedByUser(string $userId, string $accountId): bool;
}
