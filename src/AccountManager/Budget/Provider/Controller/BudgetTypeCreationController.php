<?php

namespace App\AccountManager\Budget\Provider\Controller;

use App\AccountManager\Budget\Application\Exception\ForbiddenResourceAccessException;
use App\AccountManager\Budget\Application\UseCase\BudgetTypeCreationUseCase;
use App\Core\Security\Infrastructure\Entity\User;
use App\Shared\Infrastructure\AbstractApiController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;

class BudgetTypeCreationController extends AbstractApiController
{
  #[Route(path: '/budget/type', name: 'budget_type_creation', methods: ['POST'])]
  public function __invoke(
    BudgetTypeCreationUseCase $budgetTypeCreationUseCase,
    LoggerInterface $logger,
    Request $request,
  ): JsonResponse {
    $user = $this->getUser();
    if (!($user instanceof User)) {
      throw new UnauthorizedHttpException('Basic');
    }

    $content = json_decode($request->getContent(), true);
    if ($this->isContentIncorrect($content)) {
      throw new BadRequestHttpException();
    }

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
}
