<?php

namespace App\Tests\AccountManager\Account\Provider\Service;

use App\AccountManager\Account\Provider\Entity\Account;
use App\AccountManager\Account\Provider\Service\AccountUpdateService;
use App\Shared\Domain\Exception\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class AccountUpdateServiceTest extends KernelTestCase
{
    public function testThrowsEntityNotFoundExceptionIfAccountIsNotFound(): void
    {
        self::bootKernel();
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        $this->expectException(EntityNotFoundException::class);

        $accountUpdateService = new AccountUpdateService($entityManager);
        $accountUpdateService->update(Uuid::v4(), 'new account name');
    }

    public function testAccountNameIsUpdated(): void
    {
        self::bootKernel();
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        $accountToUpdate = $entityManager
          ->getRepository(Account::class)
          ->findOneBy(['name' => 'Compte courant CCP'])
        ;

        $accountUpdateService = new AccountUpdateService($entityManager);
        $accountUpdateService->update($accountToUpdate->getId(), 'new account name');

        $accountUpdated = $entityManager
          ->getRepository(Account::class)
          ->findOneBy(['name' => 'new account name'])
        ;

        $this->assertSame($accountToUpdate->getId(), $accountUpdated->getId());
    }
}
