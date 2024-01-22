<?php

namespace App\DataFixtures;

use App\AccountManager\Budget\Infrastructure\Entity\Budget;
use App\AccountManager\Budget\Infrastructure\Entity\BudgetCategory;
use App\AccountManager\Budget\Infrastructure\Entity\BudgetType;
use App\Core\Security\Infrastructure\Entity\User;
use DateTime;
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
    $user = $this->addUser($manager);
    $this->addBudgets($manager, $user);

    $manager->flush();
  }

  private function addUser(ObjectManager $manager): User
  {
    $user = new User();
    $user->setUsername('user');
    $passwordHashed = $this->userPasswordHasher->hashPassword($user, 'password');
    $user->setPassword($passwordHashed);
    $manager->persist($user);

    return $user;
  }

  private function addBudgets(ObjectManager $manager, User $user): void
  {
    $firstCategory = new BudgetCategory();
    $firstCategory->setName('First category');
    $firstCategory->setUser($user);
    $manager->persist($firstCategory);

    $secondCategory = new BudgetCategory();
    $secondCategory->setName('Second category');
    $secondCategory->setUser($user);
    $manager->persist($secondCategory);

    $firstType = new BudgetType();
    $firstType->setName('First type');
    $firstType->setCategory($firstCategory);
    $manager->persist($firstType);

    $secondType = new BudgetType();
    $secondType->setName('Second type');
    $secondType->setCategory($firstCategory);
    $manager->persist($secondType);

    $thirdType = new BudgetType();
    $thirdType->setName('Third type');
    $thirdType->setCategory($secondCategory);
    $manager->persist($thirdType);

    $now = new DateTime();
    $month = (int) $now->format('n');
    $year = (int) $now->format('Y');

    $secondBudget = new Budget();
    $secondBudget->setType($firstType);
    $secondBudget->setMonth($month);
    $secondBudget->setYear($year);
    $secondBudget->setAssignedAmount('50.00');
    $secondBudget->setAvailableAmount('50.00');
    $manager->persist($secondBudget);

    $thirdBudget = new Budget();
    $thirdBudget->setType($secondType);
    $thirdBudget->setMonth($month);
    $thirdBudget->setYear($year);
    $thirdBudget->setAssignedAmount('25.00');
    $thirdBudget->setAvailableAmount('-10.00');
    $manager->persist($thirdBudget);
  }
}
