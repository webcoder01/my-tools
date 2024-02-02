<?php

namespace App\AccountManager\Budget\Provider\Controller;

use App\AccountManager\Budget\Application\UseCase\BudgetViewerInterface;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BudgetViewController extends AbstractController
{
  #[Route(path: '/budgets', name: 'budgets')]
  public function __invoke(BudgetViewerInterface $budgetViewer): Response
  {
    $userId = $this->getUser()->getId();
    $currentDate = new DateTime();
    $currentMonth = (int) $currentDate->format('n');
    $currentYear = (int) $currentDate->format('Y');

    return $this->render('budget/view/index.html.twig', [
      'budgets_grouped_by_categories' => $budgetViewer->getViewOfMonth($userId, $currentMonth, $currentYear),
      'navigation_dates' => $budgetViewer->getNavigationDates($currentDate),
    ]);
  }
}
