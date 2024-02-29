<?php

namespace App\Shared\Infrastructure;

use Symfony\Component\Form\FormInterface;

interface FormCreatorInterface
{
    public function createForm(EntityInterface $formData, array $options = []): FormInterface;
}
