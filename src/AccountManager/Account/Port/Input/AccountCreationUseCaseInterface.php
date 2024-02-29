<?php

namespace App\AccountManager\Account\Port\Input;

interface AccountCreationUseCaseInterface
{
    public function createOne(string $userId, string $accountName, string $startingBalance): string;
}
