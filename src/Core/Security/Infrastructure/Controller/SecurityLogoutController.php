<?php

namespace App\Core\Security\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class SecurityLogoutController extends AbstractController
{
    #[Route(path: '/deconnexion', name: 'app_core_security_logout')]
    public function __invoke(): void
    {}
}
