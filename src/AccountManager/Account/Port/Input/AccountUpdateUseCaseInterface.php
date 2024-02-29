<?php

namespace App\AccountManager\Account\Port\Input;

interface AccountUpdateUseCaseInterface
{
    public function update(string $userId, string $accountId, string $accountName): void;
}
