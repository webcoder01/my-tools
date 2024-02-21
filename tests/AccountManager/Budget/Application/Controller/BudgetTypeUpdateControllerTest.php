<?php

namespace App\Tests\AccountManager\Budget\Application\Controller;

use App\AccountManager\Budget\Infrastructure\Entity\BudgetCategory;
use App\AccountManager\Budget\Infrastructure\Entity\BudgetType;
use App\Core\Security\Infrastructure\Entity\User;
use App\Tests\AuthenticationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

class BudgetTypeUpdateControllerTest extends WebTestCase
{
  use AuthenticationTestTrait;

  public function testReturnsUnauthorizedAccessIfUserIsNotLoggedIn(): void
  {
    $client = static::createClient();
    $client->request('PUT', '/gestion-de-compte/budget/type/update');

    $this->assertResponseStatusCodeSame(401);
  }

  /**
   * @dataProvider methodsProvider
   */
  public function testReturnsMethodNotAllowedStatusCodeIfMethodIsNotPut(string $method, int $statusCode): void
  {
    $client = static::createClient();
    $this->loginUser($client, static::getContainer());
    $client->request($method, '/gestion-de-compte/budget/type/update', [], [], [], '[]');

    $this->assertResponseStatusCodeSame($statusCode);
  }

  public function methodsProvider(): array
  {
    return [
      ['GET', 405],
      ['POST', 405],
      ['DELETE', 405],
    ];
  }

  public function testReturnsBadRequestStatusCodeIfContentIsIncorrect(): void
  {
    $client = static::createClient();
    $this->loginUser($client, static::getContainer());
    $client->request('PUT', '/gestion-de-compte/budget/type/update', [], [], [], '[]');

    $this->assertResponseStatusCodeSame(400);
  }

  public function testReturnsForbiddenStatusCodeIfCategoryDoesNotBelongToUser(): void
  {
    $client = static::createClient();
    $this->loginUser($client, static::getContainer());

    $data = [
      'category_id' => Uuid::v4()->toRfc4122(),
      'type_id' => Uuid::v4()->toRfc4122(),
      'name' => 'test',
    ];
    $client->request('PUT', '/gestion-de-compte/budget/type/update', [], [], [], json_encode($data));

    $this->assertResponseStatusCodeSame(403);
  }

  public function testReturns200ResponseStatusCodeWhenUpdateIsASuccess(): void
  {
    $client = static::createClient();
    $container = static::getContainer();
    $this->loginUser($client, $container);

    $entityManager = $container->get('doctrine')->getManager();
    $user = $entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);
    $categoryOfUser = $entityManager
      ->getRepository(BudgetCategory::class)
      ->findOneBy(['user' => $user])
    ;
    $budgetTypeOfUser = $entityManager
      ->getRepository(BudgetType::class)
      ->findOneBy(['category' => $categoryOfUser])
    ;

    $data = [
      'category_id' => $categoryOfUser->getId(),
      'type_id' => $budgetTypeOfUser->getId(),
      'name' => 'budget type updated',
    ];
    $client->request('PUT', '/gestion-de-compte/budget/type/update', [], [], [], json_encode($data));

    $this->assertResponseStatusCodeSame(200);
  }
}
