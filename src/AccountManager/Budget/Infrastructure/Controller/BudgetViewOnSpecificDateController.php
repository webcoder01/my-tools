<?php

namespace App\AccountManager\Budget\Infrastructure\Controller;

use App\AccountManager\Budget\Application\UseCase\BudgetViewerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BudgetViewOnSpecificDateController extends AbstractController
{
  #[Route(path: '/budgets/{month}/{year}', name: 'budgets_on_specific_date')]
  public function __invoke(BudgetViewerInterface $budgetViewer, int $month, int $year): Response
  {
    $userId = $this->getUser()->getId();

    return $this->render('budget/view/index.html.twig', [
      'budgets_grouped_by_categories' => $budgetViewer->getViewOfMonth($userId, $month, $year),
    ]);
  }
}
