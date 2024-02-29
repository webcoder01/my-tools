<?php

namespace App\Shared\Infrastructure;

use App\Core\Security\Provider\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Uid\Uuid;

abstract class AbstractApiController extends AbstractController
{
    abstract public function getKeysAndValueTypesExpectedInContent(): array;

    abstract protected function getAuthorizedMethod(): string;

    protected function isContentIncorrect(array $contentParsed): bool
    {
        $keysAndValuesTypesExpected = $this->getKeysAndValueTypesExpectedInContent();

        if (0 === count($contentParsed)) {
            return true;
        }

        foreach ($contentParsed as $key => $value) {
            if (!isset($keysAndValuesTypesExpected[$key])) {
                return true;
            }

            if ('uuid' === $keysAndValuesTypesExpected[$key] && !Uuid::isValid($value)) {
                return true;
            }

            if ('uuid' !== $keysAndValuesTypesExpected[$key] && gettype($value) !== $keysAndValuesTypesExpected[$key]) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws UnauthorizedHttpException
     * @throws MethodNotAllowedHttpException
     * @throws BadRequestHttpException
     */
    protected function executeSecurityChecks(Request $request): void
    {
        $user = $this->getUser();
        if (!($user instanceof User)) {
            throw new UnauthorizedHttpException('Basic');
        }

        $authorizedMethod = $this->getAuthorizedMethod();
        if ($request->getMethod() !== $authorizedMethod) {
            throw new MethodNotAllowedHttpException([$authorizedMethod]);
        }

        $content = $this->getContentToArray($request);
        if ($this->isContentIncorrect($content)) {
            throw new BadRequestHttpException();
        }
    }

    protected function getContentToArray(Request $request): array
    {
        return json_decode($request->getContent(), true);
    }
}
