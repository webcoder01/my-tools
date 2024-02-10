<?php

namespace App\DataFixtures;

use App\Core\Security\Infrastructure\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
  public const DEFAULT_USER_REFERENCE = 'default-user';

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

    $this->addReference(self::DEFAULT_USER_REFERENCE, $user);
  }
}
