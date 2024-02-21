<?php

namespace App\AccountManager\Budget\Application\Controller;

use App\AccountManager\Budget\Application\Exception\ForbiddenResourceAccessException;
use App\AccountManager\Budget\Port\Input\BudgetTypeUpdateUseCaseInterface;
use App\Shared\Infrastructure\AbstractApiController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

class BudgetTypeUpdateController extends AbstractApiController
{
  #[Route(path: '/budget/type/update', name: 'budget_type_update')]
  public function __invoke(
    BudgetTypeUpdateUseCaseInterface $budgetTypeUpdateUseCase,
    LoggerInterface         $logger,
    Request                 $request
  ): JsonResponse {
    $this->executeSecurityChecks($request);
    $user = $this->getUser();
    $content = $this->getContentToArray($request);

    try {
      $budgetTypeUpdateUseCase
        ->updateBudgetType($user->getId(), $content['type_id'], $content['category_id'], $content['name'])
      ;
    } catch (ForbiddenResourceAccessException $exception) {
      $logger->error($exception);
      throw new HttpException(403);
    }

    return new JsonResponse(['id' => $content['type_id']], 200);
  }

  public function getKeysAndValueTypesExpectedInContent(): array
  {
    return [
      'category_id' => 'uuid',
      'type_id' => 'uuid',
      'name' => 'string',
    ];
  }

  protected function getAuthorizedMethod(): string
  {
    return Request::METHOD_PUT;
  }
}
