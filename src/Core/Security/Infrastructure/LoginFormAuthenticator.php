<?php

namespace App\Core\Security\Infrastructure;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
  use TargetPathTrait;

  public const LOGIN_ROUTE = 'app_core_security_login';

  private UrlGeneratorInterface $urlGenerator;

  public function __construct(UrlGeneratorInterface $urlGenerator)
  {
    $this->urlGenerator = $urlGenerator;
  }

  public function authenticate(Request $request): Passport
  {
    $requestParameters = $request->request->all()['login'];
    $username = $requestParameters['username'] ?? '';
    $password = $requestParameters['password'] ?? '';
    $token = $requestParameters['_csrf_token'] ?? null;
    $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

    return new Passport(
      new UserBadge($username),
      new PasswordCredentials($password),
      [
        new CsrfTokenBadge('authenticate', $token),
        new RememberMeBadge(),
      ]
    );
  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
  {
    if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
      return new RedirectResponse($targetPath);
    }

    return new RedirectResponse($this->urlGenerator->generate('app_core_home_index'));
  }

  protected function getLoginUrl(Request $request): string
  {
    return $this->urlGenerator->generate(self::LOGIN_ROUTE);
  }
}
