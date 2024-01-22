<?php

namespace App\Shared\Infrastructure;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractQuery
{
  protected EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }
}
