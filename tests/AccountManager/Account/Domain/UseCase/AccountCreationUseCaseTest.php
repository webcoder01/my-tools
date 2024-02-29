<?php

namespace App\Tests\AccountManager\Account\Domain\UseCase;

use App\AccountManager\Account\Domain\UseCase\AccountCreationUseCase;
use App\AccountManager\Account\Port\Output\AccountCreationServiceInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Uid\Uuid;

class AccountCreationUseCaseTest extends TestCase
{
    use ProphecyTrait;

    public function testCallsServiceThatPersistAccountInDatabase(): void
    {
        $newAccountIdExpected = Uuid::v4()->toRfc4122();
        $accountUpsertService = $this->prophesize(AccountCreationServiceInterface::class);
        $accountUpsertService->persistOne(
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('string')
        )->willReturn($newAccountIdExpected)
          ->shouldBeCalledOnce()
        ;

        $accountCreationUseCase = new AccountCreationUseCase($accountUpsertService->reveal());

        $userId = Uuid::v4()->toRfc4122();
        $accountId = $accountCreationUseCase->createOne($userId, 'new account name', '2000.00');

        $this->assertSame($newAccountIdExpected, $accountId);
    }
}
