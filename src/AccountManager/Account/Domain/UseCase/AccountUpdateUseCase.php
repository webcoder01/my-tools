<?php

namespace App\AccountManager\Account\Domain\UseCase;

use App\AccountManager\Account\Port\Input\AccountUpdateUseCaseInterface;
use App\AccountManager\Account\Port\Output\AccountByUserFinderServiceInterface;
use App\AccountManager\Account\Port\Output\AccountUpdateServiceInterface;
use App\Shared\Domain\Exception\ForbiddenResourceAccessException;

class AccountUpdateUseCase implements AccountUpdateUseCaseInterface
{
    private AccountByUserFinderServiceInterface $accountByUserFinderService;
    private AccountUpdateServiceInterface $accountUpdateService;

    public function __construct(
        AccountByUserFinderServiceInterface $accountByUserFinderService,
        AccountUpdateServiceInterface $accountUpdateService
    ) {
        $this->accountByUserFinderService = $accountByUserFinderService;
        $this->accountUpdateService = $accountUpdateService;
    }

    public function update(string $userId, string $accountId, string $accountName): void
    {
        if (!$this->accountByUserFinderService->isAccountOwnedByUser($userId, $accountId)) {
            throw new ForbiddenResourceAccessException('account');
        }

        $this->accountUpdateService->update($accountId, $accountName);
    }
}
