<?php

namespace App\Tests\AccountManager\Budget\Provider\Controller;

use App\Tests\AuthenticationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

class BudgetTypeCreationControllerTest extends WebTestCase
{
  use AuthenticationTestTrait;

  public function testReturnsUnauthorizedAccessIfUserIsNotLoggedIn(): void
  {
    $client = static::createClient();
    $client->request('POST', '/gestion-de-compte/budget/type');

    $this->assertResponseStatusCodeSame(401);
  }

  /**
   * @dataProvider methodsProvider
   */
  public function testReturnsMethodNotAllowedStatusCodeIfMethodIsNotPost(string $method, int $statusCode): void
  {
    $client = static::createClient();
    $this->loginUser($client, static::getContainer());
    $client->request($method, '/gestion-de-compte/budget/type', [], [], [], '[]');

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
    $client->request('POST', '/gestion-de-compte/budget/type', [], [], [], '[]');

    $this->assertResponseStatusCodeSame(400);
  }

  public function testReturnsForbiddenStatusCodeIfCategoryDoesNotBelongToUser(): void
  {
    $client = static::createClient();
    $this->loginUser($client, static::getContainer());

    $content = json_encode(['category_id' => Uuid::v4()->toRfc4122(), 'name' => 'test']);
    $client->request('POST', '/gestion-de-compte/budget/type', [], [], [], $content);

    $this->assertResponseStatusCodeSame(403);
  }
}