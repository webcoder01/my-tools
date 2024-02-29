<?php

namespace App\Tests\AccountManager\Budget\Application\Controller;

use App\Core\Security\Provider\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BudgetViewOnSpecificDateControllerTest extends WebTestCase
{
    public function testRedirectToAuthenticationIfUserIsNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/gestion-de-compte/budgets/1/2024');

        $this->assertResponseRedirects('/connexion', 302);
    }

    public function testRouteNotFoundIfMonthIsNotADigit(): void
    {
        $client = static::createClient();
        $client->request('GET', '/gestion-de-compte/budgets/string/2024');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testRouteNotFoundIfMonthIsLowerThanOne(): void
    {
        $client = static::createClient();
        $client->request('GET', '/gestion-de-compte/budgets/0/2024');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testRouteNotFoundIfMonthIsGreaterThanTwelve(): void
    {
        $client = static::createClient();
        $client->request('GET', '/gestion-de-compte/budgets/13/2024');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testRouteNotFoundIfYearIsNotADigit(): void
    {
        $client = static::createClient();
        $client->request('GET', '/gestion-de-compte/budgets/1/string');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testNavigationDatesAreFromDatesSpecifiedInUrl(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);
        $client->loginUser($user);

        $client->request('GET', '/gestion-de-compte/budgets');

        $this->assertResponseIsSuccessful();
        $this->assertAnySelectorTextSame('li', 'Janvier 2024');
    }
}
