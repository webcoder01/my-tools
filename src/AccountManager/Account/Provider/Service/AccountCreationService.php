<?php

namespace App\AccountManager\Account\Provider\Service;

use App\AccountManager\Account\Port\Output\AccountCreationServiceInterface;
use App\AccountManager\Account\Provider\Entity\Account;
use App\Core\Security\Provider\Entity\User;
use App\Shared\Provider\AbstractService;

class AccountCreationService extends AbstractService implements AccountCreationServiceInterface
{
    public function persistOne(string $userId, string $accountName, string $startingBalance): string
    {
        $account = new Account();
        $account->setUser($this->getUserById($userId));
        $account->setName($accountName);
        $account->setBalance($startingBalance);

        $this->entityManager->persist($account);
        $this->entityManager->flush();

        return $account->getId();
    }

    private function getUserById(string $userId): User
    {
        return $this->entityManager
          ->getRepository(User::class)
          ->find($userId)
        ;
    }
}
