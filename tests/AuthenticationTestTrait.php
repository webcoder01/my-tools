<?php

namespace App\Tests;

use App\Core\Security\Provider\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\Container;

trait AuthenticationTestTrait
{
  public User $user;

  public function loginUser(KernelBrowser $client, Container $container): void
  {
    $entityManager = $container->get('doctrine')->getManager();
    $this->user = $entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);
    $client->loginUser($this->user);
  }
}
