<?php

namespace App\AccountManager\Budget\Application\Controller;

use App\AccountManager\Budget\Port\Input\BudgetsInitializationUseCaseInterface;
use App\Shared\Infrastructure\AbstractApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class BudgetsInitializationController extends AbstractApiController
{
  #[Route(path: 'budget/initialise', name: 'budget_initiate')]
  public function __invoke(
    BudgetsInitializationUseCaseInterface $budgetsInitializationUseCase,
    Request $request
  ): JsonResponse {
    $this->executeSecurityChecks($request);
    $user = $this->getUser();
    $content = $this->getContentToArray($request);

    $budgetsInitializationUseCase->initiate($user->getId(), $content['month'], $content['year']);

    return new JsonResponse([], 200);
  }

  public function getKeysAndValueTypesExpectedInContent(): array
  {
    return [
      'month' => 'integer',
      'year' => 'integer',
    ];
  }

  protected function getAuthorizedMethod(): string
  {
    return Request::METHOD_POST;
  }
}
