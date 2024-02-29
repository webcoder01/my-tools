<?php

namespace App\AccountManager\Account\Provider\Service;

use App\AccountManager\Account\Port\Output\AccountByUserFinderServiceInterface;
use App\AccountManager\Account\Provider\Entity\Account;
use App\Shared\Provider\AbstractService;

class AccountByUserFinderService extends AbstractService implements AccountByUserFinderServiceInterface
{
    public function isAccountOwnedByUser(string $userId, string $accountId): bool
    {
        $account = $this->entityManager
          ->getRepository(Account::class)
          ->findOneBy([
            'id' => $accountId,
            'user' => $userId,
          ])
        ;

        return $account instanceof Account;
    }
}
