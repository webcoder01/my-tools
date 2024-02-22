<?php

namespace App\Tests\AccountManager\Account\Application\Controller;

use App\Tests\AuthenticationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountCreationControllerTest extends WebTestCase
{
  use AuthenticationTestTrait;

  public function testReturnsUnauthorizedAccessIfUserIsNotLoggedIn(): void
  {
    $client = static::createClient();
    $client->request('POST', '/gestion-de-compte/compte/creation');

    $this->assertResponseStatusCodeSame(401);
  }

  /**
   * @dataProvider methodsProvider
   */
  public function testReturnsMethodNotAllowedStatusCodeIfMethodIsNotPost(string $method, int $statusCode): void
  {
    $client = static::createClient();
    $this->loginUser($client, static::getContainer());
    $client->request($method, '/gestion-de-compte/compte/creation', [], [], [], '[]');

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
    $client->request('POST', '/gestion-de-compte/compte/creation', [], [], [], '[]');

    $this->assertResponseStatusCodeSame(400);
  }

  public function testReturns201ResponseStatusCodeWhenCreationIsASuccess(): void
  {
    $client = static::createClient();
    $this->loginUser($client, static::getContainer());
    $content = json_encode(['name' => 'New account name', 'starting_balance' => '2000.00']);
    $client->request('POST', '/gestion-de-compte/compte/creation', [], [], [], $content);

    $this->assertResponseStatusCodeSame(201);
  }
}
