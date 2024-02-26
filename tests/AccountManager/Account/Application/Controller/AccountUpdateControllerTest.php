<?php

namespace App\Tests\AccountManager\Account\Application\Controller;

use App\AccountManager\Account\Provider\Entity\Account;
use App\Tests\AuthenticationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

class AccountUpdateControllerTest extends WebTestCase
{
  use AuthenticationTestTrait;

  public function testReturnsUnauthorizedAccessIfUserIsNotLoggedIn(): void
  {
    $client = static::createClient();
    $client->request('PUT', '/gestion-de-compte/compte/edition');

    $this->assertResponseStatusCodeSame(401);
  }

  /**
   * @dataProvider methodsProvider
   */
  public function testReturnsMethodNotAllowedStatusCodeIfMethodIsNotPut(string $method, int $statusCode): void
  {
    $client = static::createClient();
    $this->loginUser($client, static::getContainer());
    $client->request($method, '/gestion-de-compte/compte/edition', [], [], [], '[]');

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
    $client->request('PUT', '/gestion-de-compte/compte/edition', [], [], [], '[]');

    $this->assertResponseStatusCodeSame(400);
  }

  public function testReturns200ResponseStatusCodeWhenUpdateIsASuccess(): void
  {
    $client = static::createClient();
    $container = static::getContainer();
    $this->loginUser($client, $container);

    $accountToUpdate = $container->get('doctrine')->getManager()
      ->getRepository(Account::class)
      ->findOneBy(['name' => 'Compte courant CCP'])
    ;

    $content = json_encode(['account_id' => $accountToUpdate->getId(), 'name' => 'New account name']);
    $client->request('PUT', '/gestion-de-compte/compte/edition', [], [], [], $content);

    $this->assertResponseStatusCodeSame(200);
  }
}
