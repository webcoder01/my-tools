<?php

namespace App\Core\Security\Application\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityLoginController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_core_security_login')]
    public function __invoke(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('app_core_home_index');
         }

        $lastError = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('core/security/login.html.twig', [
            'last_username' => $lastUsername,
            'last_error' => $lastError,
        ]);
    }
}