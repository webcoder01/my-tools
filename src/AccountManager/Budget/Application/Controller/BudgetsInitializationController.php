<?php

namespace App\AccountManager\Budget\Application\Controller;

use App\Shared\Infrastructure\AbstractApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class BudgetsInitializationController extends AbstractApiController
{
  #[Route(path: 'budget/initiate', name: 'budget_initiate')]
  public function __invoke(Request $request): JsonResponse
  {
    $this->executeSecurityChecks($request);

    return new JsonResponse([], 200);
  }

  public function getKeysAndValueTypesExpectedInContent(): array
  {
    return [
      'month' => 'int',
      'year' => 'int',
    ];
  }

  protected function getAuthorizedMethod(): string
  {
    return Request::METHOD_POST;
  }
}
