<?php

namespace App\AccountManager\Account\Port\Output;

interface AccountUpdateServiceInterface
{
    public function update(string $accountId, string $accountName): void;
}
