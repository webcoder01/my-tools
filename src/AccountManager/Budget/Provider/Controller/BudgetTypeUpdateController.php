<?php

namespace App\AccountManager\Budget\Provider\Controller;

use App\AccountManager\Budget\Application\Exception\ForbiddenResourceAccessException;
use App\AccountManager\Budget\Port\Input\BudgetTypeUpdateUseCaseInterface;
use App\Core\Security\Infrastructure\Entity\User;
use App\Shared\Infrastructure\AbstractApiController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;

class BudgetTypeUpdateController extends AbstractApiController
{
  #[Route(path: '/budget/type/update', name: 'budget_type_update', methods: ['PUT'])]
  public function __invoke(
    BudgetTypeUpdateUseCaseInterface $budgetTypeUpdateUseCase,
    LoggerInterface         $logger,
    Request                 $request
  ): JsonResponse {
    $user = $this->getUser();
    if (!($user instanceof User)) {
      throw new UnauthorizedHttpException('Basic');
    }

    if ($request->getMethod() !== Request::METHOD_PUT) {
      throw new MethodNotAllowedHttpException([Request::METHOD_PUT]);
    }

    $content = json_decode($request->getContent(), true);
    if ($this->isContentIncorrect($content)) {
      throw new BadRequestHttpException();
    }

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
}
