<?php

namespace App\DataFixtures;

use App\Core\Security\Infrastructure\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
  private UserPasswordHasherInterface $userPasswordHasher;

  public function __construct(UserPasswordHasherInterface $userPasswordHasher)
  {
    $this->userPasswordHasher = $userPasswordHasher;
  }

  public function load(ObjectManager $manager): void
  {
    $user = new User();
    $user->setUsername('user');

    $passwordHashed = $this->userPasswordHasher->hashPassword($user, 'password');
    $user->setPassword($passwordHashed);

    $manager->persist($user);

    $manager->flush();
  }
}
