<?php

namespace App\Tests\AccountManager\Account\Domain\UseCase;

use App\AccountManager\Account\Domain\UseCase\AccountUpdateUseCase;
use App\AccountManager\Account\Port\Output\AccountByUserFinderServiceInterface;
use App\AccountManager\Account\Port\Output\AccountUpdateServiceInterface;
use App\Shared\Domain\Exception\ForbiddenResourceAccessException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Uid\Uuid;

class AccountUpdateUseCaseTest extends TestCase
{
    use ProphecyTrait;

    public function testThrowsForbiddenResourceAccessExceptionIfUserDoesNotOwnAccount(): void
    {
        $accountByUserFinderService = $this->prophesize(AccountByUserFinderServiceInterface::class);
        $accountByUserFinderService
          ->isAccountOwnedByUser(Argument::type('string'), Argument::type('string'))
          ->willReturn(false)
        ;

        $accountUpdateService = $this->prophesize(AccountUpdateServiceInterface::class);

        $this->expectException(ForbiddenResourceAccessException::class);

        $accountUpdateUseCase = new AccountUpdateUseCase(
            $accountByUserFinderService->reveal(),
            $accountUpdateService->reveal()
        );
        $accountUpdateUseCase->update(Uuid::v4(), Uuid::v4(), 'new account name');
    }

    public function testCallsServiceThatUpdateAccountInDatabase(): void
    {
        $accountByUserFinderService = $this->prophesize(AccountByUserFinderServiceInterface::class);
        $accountByUserFinderService
          ->isAccountOwnedByUser(Argument::type('string'), Argument::type('string'))
          ->willReturn(true)
        ;

        $accountUpdateService = $this->prophesize(AccountUpdateServiceInterface::class);
        $accountUpdateService
          ->update(Argument::type('string'), Argument::type('string'))
          ->shouldBeCalledOnce()
        ;

        $accountUpdateUseCase = new AccountUpdateUseCase(
            $accountByUserFinderService->reveal(),
            $accountUpdateService->reveal()
        );
        $accountUpdateUseCase->update(Uuid::v4(), Uuid::v4(), 'new account name');
    }
}
