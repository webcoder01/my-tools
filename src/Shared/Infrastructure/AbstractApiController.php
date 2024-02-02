<?php

namespace App\Shared\Infrastructure;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Uid\Uuid;

abstract class AbstractApiController extends AbstractController
{
  abstract public function getKeysAndValueTypesExpectedInContent(): array;

  protected function isContentIncorrect(array $contentParsed): bool
  {
    $keysAndValuesTypesExpected = $this->getKeysAndValueTypesExpectedInContent();

    if (count($contentParsed) === 0) {
      return true;
    }

    foreach ($contentParsed as $key => $value) {
      if (!isset($keysAndValuesTypesExpected[$key])) {
        return true;
      }

      if ($keysAndValuesTypesExpected[$key] === 'uuid' && !Uuid::isValid($value)) {
        return true;
      }

      if ($keysAndValuesTypesExpected[$key] !== 'uuid' && gettype($value) !== $keysAndValuesTypesExpected[$key]) {
        return true;
      }
    }

    return false;
  }
}
