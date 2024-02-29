<?php

namespace App\Tests\AccountManager\Budget\Application\Controller;

use App\Tests\AuthenticationTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BudgetsInitializationControllerTest extends WebTestCase
{
    use AuthenticationTestTrait;

    public function testReturnsUnauthorizedAccessIfUserIsNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('POST', '/gestion-de-compte/budget/initialise');

        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @dataProvider methodsProvider
     */
    public function testReturnsMethodNotAllowedStatusCodeIfMethodIsNotPost(string $method, int $statusCode): void
    {
        $client = static::createClient();
        $this->loginUser($client, static::getContainer());
        $client->request($method, '/gestion-de-compte/budget/initialise', [], [], [], '[]');

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
        $client->request('POST', '/gestion-de-compte/budget/initialise', [], [], [], '[]');

        $this->assertResponseStatusCodeSame(400);
    }

    public function testReturns200ResponseStatusCodeWhenBudgetIsInitialized(): void
    {
        $client = static::createClient();
        $this->loginUser($client, static::getContainer());

        $nextMonthDate = new \DateTime('next month');
        $content = json_encode([
          'month' => (int) $nextMonthDate->format('n'),
          'year' => (int) $nextMonthDate->format('Y'),
        ]);

        $client->request('POST', '/gestion-de-compte/budget/initialise', [], [], [], $content);

        $this->assertResponseStatusCodeSame(200);
    }
}
