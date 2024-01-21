<?php

namespace App\Core\Security\Infrastructure\Controller;

use App\Core\Security\Infrastructure\Entity\User;
use App\Core\Security\Infrastructure\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityLoginController extends AbstractController
{
  #[Route(path: '/connexion', name: 'app_core_security_login')]
  public function __invoke(AuthenticationUtils $authenticationUtils, LoginType $loginType): Response
  {
    if ($this->getUser()) {
      return $this->redirectToRoute('app_core_home_index');
    }

    $user = new User();
    $user->setUsername($authenticationUtils->getLastUsername());
    $form = $loginType->createForm($user);
    $lastError = $authenticationUtils->getLastAuthenticationError();

    return $this->render('core/security/login.html.twig', [
      'form' => $form->createView(),
      'last_error' => $lastError,
    ]);
  }
}
