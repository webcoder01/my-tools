<?php

namespace App\AccountManager\Account\Application\Controller;

use App\AccountManager\Account\Port\Input\AccountCreationUseCaseInterface;
use App\Shared\Infrastructure\AbstractApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AccountCreationController extends AbstractApiController
{
  private AccountCreationUseCaseInterface $accountCreationUseCase;

  public function __construct(AccountCreationUseCaseInterface $accountCreationUseCase)
  {
    $this->accountCreationUseCase = $accountCreationUseCase;
  }

  #[Route(path: '/creation', name: 'creation')]
  public function __invoke(Request $request): JsonResponse
  {
    $this->executeSecurityChecks($request);
    $user = $this->getUser();
    $content = $this->getContentToArray($request);

    $newAccountId = $this->accountCreationUseCase->createOne(
      $user->getId(),
      $content['name'],
      $content['starting_balance']
    );

    return new JsonResponse(['id' => $newAccountId], 201);
  }

  public function getKeysAndValueTypesExpectedInContent(): array
  {
    return [
      'name' => 'string',
      'starting_balance' => 'string',
    ];
  }

  protected function getAuthorizedMethod(): string
  {
    return Request::METHOD_POST;
  }
}
