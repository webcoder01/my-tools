<?php

namespace App\AccountManager\Budget\Application\Controller;

use App\AccountManager\Budget\Port\Input\BudgetViewerUseCaseInterface;
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
    public function __invoke(BudgetViewerUseCaseInterface $budgetViewerUseCase, int $month, int $year): Response
    {
        $userId = $this->getUser()->getId();
        $date = \DateTime::createFromFormat('d-m-Y', sprintf('1-%d-%d', $month, $year));

        return $this->render('budget/view/index.html.twig', [
          'budgets_grouped_by_categories' => $budgetViewerUseCase->getViewOfMonth($userId, $month, $year),
          'navigation_dates' => $budgetViewerUseCase->getNavigationDates($date),
        ]);
    }
}
