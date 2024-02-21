<?php

namespace App\Tests\AccountManager\Budget\Application\Controller;

use App\AccountManager\Budget\Provider\Entity\BudgetCategory;
use App\Tests\AuthenticationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

class BudgetTypeCreationControllerTest extends WebTestCase
{
  use AuthenticationTestTrait;

  public function testReturnsUnauthorizedAccessIfUserIsNotLoggedIn(): void
  {
    $client = static::createClient();
    $client->request('POST', '/gestion-de-compte/budget/type/create');

    $this->assertResponseStatusCodeSame(401);
  }

  /**
   * @dataProvider methodsProvider
   */
  public function testReturnsMethodNotAllowedStatusCodeIfMethodIsNotPost(string $method, int $statusCode): void
  {
    $client = static::createClient();
    $this->loginUser($client, static::getContainer());
    $client->request($method, '/gestion-de-compte/budget/type/create', [], [], [], '[]');

    $this->assertResponseStatusCodeSame($statusCode);
  }

  public function methodsProvider(): array
  {
    return [
      ['GET', 405],
      ['PUT', 405],
      ['DELETE', 405],
    ];
  }

  public function testReturnsBadRequestStatusCodeIfContentIsIncorrect(): void
  {
    $client = static::createClient();
    $this->loginUser($client, static::getContainer());
    $client->request('POST', '/gestion-de-compte/budget/type/create', [], [], [], '[]');

    $this->assertResponseStatusCodeSame(400);
  }

  public function testReturnsForbiddenStatusCodeIfCategoryDoesNotBelongToUser(): void
  {
    $client = static::createClient();
    $this->loginUser($client, static::getContainer());

    $content = json_encode(['category_id' => Uuid::v4()->toRfc4122(), 'name' => 'test']);
    $client->request('POST', '/gestion-de-compte/budget/type/create', [], [], [], $content);

    $this->assertResponseStatusCodeSame(403);
  }

  public function testReturns201ResponseStatusCodeWhenCreationIsASuccess(): void
  {
    $client = static::createClient();
    $container = static::getContainer();
    $this->loginUser($client, $container);

    $entityManager = $container->get('doctrine')->getManager();
    $budgetCategoryId = $entityManager
      ->getRepository(BudgetCategory::class)
      ->findOneBy(['user' => $this->user])
      ->getId()
    ;

    $content = json_encode(['category_id' => $budgetCategoryId, 'name' => 'test']);
    $client->request('POST', '/gestion-de-compte/budget/type/create', [], [], [], $content);

    $this->assertResponseStatusCodeSame(201);
  }
}
