<?php

namespace App\Shared\Infrastructure;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;

abstract class AbstractAppForm extends AbstractType
{
    protected FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }
}
