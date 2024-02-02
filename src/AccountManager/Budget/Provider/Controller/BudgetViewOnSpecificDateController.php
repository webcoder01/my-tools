<?php

namespace App\AccountManager\Budget\Provider\Controller;

use App\AccountManager\Budget\Application\UseCase\BudgetViewerInterface;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BudgetViewOnSpecificDateController extends AbstractController
{
  #[Route(
    path: '/budgets/{month}/{year}',
    name: 'budgets_on_specific_date',
    requirements: ['month' => '\d+', 'year' => '\d+'],
    condition: "params['month'] >= 1 & params['month'] <= 12"
  )]
  public function __invoke(BudgetViewerInterface $budgetViewer, int $month, int $year): Response
  {
    $userId = $this->getUser()->getId();
    $date = DateTime::createFromFormat('d-m-Y', sprintf('1-%d-%d', $month, $year));

    return $this->render('budget/view/index.html.twig', [
      'budgets_grouped_by_categories' => $budgetViewer->getViewOfMonth($userId, $month, $year),
      'navigation_dates' => $budgetViewer->getNavigationDates($date),
    ]);
  }
}
