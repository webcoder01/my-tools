<?php

namespace App\Shared\Provider;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractService
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
