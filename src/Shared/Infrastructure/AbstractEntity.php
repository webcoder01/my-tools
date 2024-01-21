<?php

namespace App\Shared\Infrastructure;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

abstract class AbstractEntity
{
  #[ORM\Id]
  #[ORM\Column(type: 'string',  length: 36)]
  protected string $id;

  public function __construct()
  {
    $this->id = Uuid::v4()->toRfc4122();
  }
}
