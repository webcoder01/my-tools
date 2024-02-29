<?php

namespace App\Tests\AccountManager\Budget\Application\Controller;

use App\Core\Security\Provider\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BudgetViewControllerTest extends WebTestCase
{
    use ProphecyTrait;

    public function testRedirectToAuthenticationIfUserIsNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/gestion-de-compte/budgets');

        $this->assertResponseRedirects('/connexion', 302);
    }

    public function testNavigationDatesAreFromCurrentDate(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $translation = static::getContainer()->get('translator');
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);
        $client->loginUser($user);

        $client->request('GET', '/gestion-de-compte/budgets');

        $this->assertResponseIsSuccessful();

        $currentDate = new \DateTime();
        $expectedMonth = $translation->trans(sprintf('months.%s', $currentDate->format('F')));
        $expectedYear = $currentDate->format('Y');
        $expectedDate = sprintf('%s %s', $expectedMonth, $expectedYear);
        $this->assertAnySelectorTextSame('li', $expectedDate);
    }
}
