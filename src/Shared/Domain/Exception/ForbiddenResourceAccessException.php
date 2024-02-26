<?php

namespace App\Shared\Domain\Exception;

use Exception;

class ForbiddenResourceAccessException extends Exception
{
  public function __construct(string $forbiddenResource)
  {
    parent::__construct(sprintf("User does not have access to resource '%s'", $forbiddenResource));
  }
}
