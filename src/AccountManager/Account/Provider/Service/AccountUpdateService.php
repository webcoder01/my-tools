<?php

namespace App\AccountManager\Account\Provider\Service;

use App\AccountManager\Account\Port\Output\AccountUpdateServiceInterface;
use App\AccountManager\Account\Provider\Entity\Account;
use App\Shared\Domain\Exception\EntityNotFoundException;
use App\Shared\Provider\AbstractService;

class AccountUpdateService extends AbstractService implements AccountUpdateServiceInterface
{
  public function update(string $accountId, string $accountName): void
  {
    $account = $this->entityManager
      ->getRepository(Account::class)
      ->find($accountId)
    ;

    if (!($account instanceof Account)) {
      throw new EntityNotFoundException(Account::class, self::class);
    }

    $account->setName($accountName);
    $this->entityManager->flush();
  }
}
