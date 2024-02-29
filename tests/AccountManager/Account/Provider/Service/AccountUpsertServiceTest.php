<?php

namespace App\Tests\AccountManager\Account\Provider\Service;

use App\AccountManager\Account\Provider\Entity\Account;
use App\AccountManager\Account\Provider\Service\AccountCreationService;
use App\Core\Security\Provider\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class AccountUpsertServiceTest extends KernelTestCase
{
    public function testNewAccountIsInsertedInDatabase(): void
    {
        self::bootKernel();
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);

        $accountUpsertService = new AccountCreationService($entityManager);
        $newAccountId = $accountUpsertService->persistOne($user->getId(), 'new account name', '2000.00');

        $accountInserted = $entityManager
          ->getRepository(Account::class)
          ->findOneBy(['name' => 'new account name'])
        ;

        $this->assertNotNull($accountInserted);
        $this->assertTrue(Uuid::isValid($newAccountId));
    }
}
