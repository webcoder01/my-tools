<?php

namespace App\AccountManager\Budget\Application\Controller;

use App\AccountManager\Budget\Domain\Exception\ForbiddenResourceAccessException;
use App\AccountManager\Budget\Port\Input\BudgetTypeCreationUseCaseInterface;
use App\Shared\Infrastructure\AbstractApiController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

class BudgetTypeCreationController extends AbstractApiController
{
  #[Route(path: '/budget/type/create', name: 'budget_type_creation')]
  public function __invoke(
    BudgetTypeCreationUseCaseInterface $budgetTypeCreationUseCase,
    LoggerInterface $logger,
    Request $request,
  ): JsonResponse {
    $this->executeSecurityChecks($request);
    $user = $this->getUser();
    $content = $this->getContentToArray($request);

    try {
      $budgetTypeIdCreated = $budgetTypeCreationUseCase
        ->createBudgetType($user->getId(), $content['category_id'], $content['name'])
      ;
    } catch (ForbiddenResourceAccessException $exception) {
      $logger->error($exception);
      throw new HttpException(403);
    }

    return new JsonResponse(['id' => $budgetTypeIdCreated], 201);
  }

  public function getKeysAndValueTypesExpectedInContent(): array
  {
    return [
      'category_id' => 'uuid',
      'name' => 'string',
    ];
  }

  protected function getAuthorizedMethod(): string
  {
    return Request::METHOD_POST;
  }
}
