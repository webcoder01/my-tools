<?php

namespace App\Tests;

use App\Core\Security\Infrastructure\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\Container;

trait AuthenticationTestTrait
{
  public function loginUser(KernelBrowser $client, Container $container): void
  {
    $entityManager = $container->get('doctrine')->getManager();
    $user = $entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);
    $client->loginUser($user);
  }
}
