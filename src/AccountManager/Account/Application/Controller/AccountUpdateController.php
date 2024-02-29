<?php

namespace App\AccountManager\Account\Application\Controller;

use App\AccountManager\Account\Port\Input\AccountUpdateUseCaseInterface;
use App\Shared\Infrastructure\AbstractApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AccountUpdateController extends AbstractApiController
{
    private AccountUpdateUseCaseInterface $accountUpdateUseCase;

    public function __construct(AccountUpdateUseCaseInterface $accountUpdateUseCase)
    {
        $this->accountUpdateUseCase = $accountUpdateUseCase;
    }

    #[Route(path: '/edition', name: 'update')]
    public function __invoke(Request $request): JsonResponse
    {
        $this->executeSecurityChecks($request);
        $user = $this->getUser();
        $content = $this->getContentToArray($request);

        $this->accountUpdateUseCase->update($user->getId(), $content['account_id'], $content['name']);

        return new JsonResponse([], 200);
    }

    public function getKeysAndValueTypesExpectedInContent(): array
    {
        return [
          'account_id' => 'uuid',
          'name' => 'string',
        ];
    }

    protected function getAuthorizedMethod(): string
    {
        return Request::METHOD_PUT;
    }
}
