<?php

namespace App\Core\Home\Application\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'app_core_home_index')]
    public function __invoke(): Response
    {
        return $this->render('core/home/index.html.twig');
    }
}