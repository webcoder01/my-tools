<?php

namespace App\AccountManager\Transaction\Provider\Entity;

use App\Shared\Infrastructure\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'transaction')]
class Transaction extends AbstractEntity
{

}
