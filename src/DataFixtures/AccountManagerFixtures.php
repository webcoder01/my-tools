<?php

namespace App\DataFixtures;

use App\AccountManager\Account\Provider\Entity\Account;
use App\AccountManager\Budget\Provider\Entity\Budget;
use App\AccountManager\Budget\Provider\Entity\BudgetCategory;
use App\AccountManager\Budget\Provider\Entity\BudgetType;
use App\AccountManager\Transaction\Provider\Entity\Transaction;
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

  private Account $mainAccount;
  private Account $savingsAccount;

  public function load(ObjectManager $manager): void
  {
    $this->loadBudgetCategories($manager);
    $this->loadBudgetTypes($manager);
    $this->loadBudgets($manager);
    $this->loadAccounts($manager);
    $this->loadTransactions($manager);

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
    $manager->persist($vacationBudget);
  }

  private function loadAccounts(ObjectManager $manager): void
  {
    $this->mainAccount = new Account();
    $this->mainAccount->setName('Compte courant CCP');
    $this->mainAccount->setBalance('2000.00');
    $this->mainAccount->setUser($this->getReference(UserFixtures::DEFAULT_USER_REFERENCE));
    $manager->persist($this->mainAccount);

    $this->savingsAccount = new Account();
    $this->savingsAccount->setName('Livret A');
    $this->savingsAccount->setBalance('10000.00');
    $this->savingsAccount->setUser($this->getReference(UserFixtures::DEFAULT_USER_REFERENCE));
    $manager->persist($this->savingsAccount);
  }

  private function loadTransactions(ObjectManager $manager): void
  {
    $firstVacationTransaction = new Transaction();
    $firstVacationTransaction->setPayee('Auchan');
    $firstVacationTransaction->setAmount('-50.99');
    $firstVacationTransaction->setChecked(true);
    $firstVacationTransaction->setBudgetType($this->vacationType);
    $firstVacationTransaction->setAccount($this->mainAccount);
    $manager->persist($firstVacationTransaction);

    $secondVacationTransaction = new Transaction();
    $secondVacationTransaction->setPayee('Formule 1');
    $secondVacationTransaction->setAmount('-120.00');
    $secondVacationTransaction->setComment('Séjour à l\'hôtel');
    $secondVacationTransaction->setBudgetType($this->vacationType);
    $secondVacationTransaction->setAccount($this->mainAccount);
    $manager->persist($secondVacationTransaction);

    $thirdVacationTransaction = new Transaction();
    $thirdVacationTransaction->setPayee('Dominos Pizza');
    $thirdVacationTransaction->setAmount('-12.50');
    $thirdVacationTransaction->setBudgetType($this->vacationType);
    $thirdVacationTransaction->setAccount($this->mainAccount);
    $manager->persist($thirdVacationTransaction);

    $fourthVacationTransaction = new Transaction();
    $fourthVacationTransaction->setPayee('Circuit tour');
    $fourthVacationTransaction->setAmount('-101.51');
    $fourthVacationTransaction->setComment('Stage de pilotage sur Porsche');
    $fourthVacationTransaction->setBudgetType($this->vacationType);
    $fourthVacationTransaction->setAccount($this->mainAccount);
    $manager->persist($fourthVacationTransaction);

    $savingsTransaction = new Transaction();
    $savingsTransaction->setPayee('Banque');
    $savingsTransaction->setAmount('200.00');
    $savingsTransaction->setAccount($this->savingsAccount);
    $manager->persist($savingsTransaction);
  }
}
