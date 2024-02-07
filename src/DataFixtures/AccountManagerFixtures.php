<?php

namespace App\DataFixtures;

use App\AccountManager\Budget\Infrastructure\Entity\Budget;
use App\AccountManager\Budget\Infrastructure\Entity\BudgetCategory;
use App\AccountManager\Budget\Infrastructure\Entity\BudgetType;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AccountManagerFixtures extends Fixture implements DependentFixtureInterface
{
  private BudgetCategory $insuranceCategory;
  private BudgetCategory $savingsCategory;

  private BudgetType $autoInsuranceType;
  private BudgetType $houseInsuranceType;
  private BudgetType $vacationType;

  public function load(ObjectManager $manager): void
  {
    $this->loadBudgetCategories($manager);
    $this->loadBudgetTypes($manager);
    $this->loadBudgets($manager);

    $manager->flush();
  }

  public function getDependencies(): array
  {
    return [
      UserFixtures::class,
    ];
  }

  private function loadBudgetCategories(ObjectManager $manager): void
  {
    $user = $this->getReference(UserFixtures::DEFAULT_USER_REFERENCE);

    $this->insuranceCategory = new BudgetCategory();
    $this->insuranceCategory->setName('Assurances');
    $this->insuranceCategory->setUser($user);
    $manager->persist($this->insuranceCategory);

    $this->savingsCategory = new BudgetCategory();
    $this->savingsCategory->setName('Epargnes');
    $this->savingsCategory->setUser($user);
    $manager->persist($this->savingsCategory);
  }

  private function loadBudgetTypes(ObjectManager $manager): void
  {
    $this->autoInsuranceType = new BudgetType();
    $this->autoInsuranceType->setName('Assurance auto');
    $this->autoInsuranceType->setCategory($this->insuranceCategory);
    $manager->persist($this->autoInsuranceType);

    $this->houseInsuranceType = new BudgetType();
    $this->houseInsuranceType->setName('Assurance habitation');
    $this->houseInsuranceType->setCategory($this->insuranceCategory);
    $manager->persist($this->houseInsuranceType);

    $this->vacationType = new BudgetType();
    $this->vacationType->setName('Vacances');
    $this->vacationType->setCategory($this->savingsCategory);
    $manager->persist($this->vacationType);
  }

  private function loadBudgets(ObjectManager $manager): void
  {
    $now = new DateTime();
    $month = (int) $now->format('n');
    $year = (int) $now->format('Y');

    $autoInsuranceBudget = new Budget();
    $autoInsuranceBudget->setType($this->autoInsuranceType);
    $autoInsuranceBudget->setMonth($month);
    $autoInsuranceBudget->setYear($year);
    $autoInsuranceBudget->setAssignedAmount('80.00');
    $autoInsuranceBudget->setAvailableAmount('80.00');
    $manager->persist($autoInsuranceBudget);

    $houseInsuranceBudget = new Budget();
    $houseInsuranceBudget->setType($this->houseInsuranceType);
    $houseInsuranceBudget->setMonth($month);
    $houseInsuranceBudget->setYear($year);
    $houseInsuranceBudget->setAssignedAmount('25.00');
    $houseInsuranceBudget->setAvailableAmount('25.00');
    $manager->persist($houseInsuranceBudget);

    $vacationBudget = new Budget();
    $vacationBudget->setType($this->vacationType);
    $vacationBudget->setMonth($month);
    $vacationBudget->setYear($year);
    $vacationBudget->setAssignedAmount('200.00');
    $vacationBudget->setAvailableAmount('-25.00');
  }
}
