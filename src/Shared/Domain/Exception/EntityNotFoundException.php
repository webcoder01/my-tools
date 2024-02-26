<?php

namespace App\Shared\Domain\Exception;

use Exception;

class EntityNotFoundException extends Exception
{
  public function __construct(string $entityClass, string $serviceAsOrigin)
  {
    parent::__construct(sprintf('Entity %s not found. Origin is %s', $entityClass, $serviceAsOrigin));
  }
}
