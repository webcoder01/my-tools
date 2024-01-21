<?php

namespace App\Core\Security\Infrastructure\Form;

use App\Core\Security\Infrastructure\Entity\User;
use App\Shared\Infrastructure\AbstractAppForm;
use App\Shared\Infrastructure\EntityInterface;
use App\Shared\Infrastructure\FormCreatorInterface;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LoginType extends AbstractAppForm implements FormCreatorInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
              'label' => 'Pseudo',
              'attr' => [
                'autofocus' => 'autofocus'
              ]
            ])
            ->add('password', PasswordType::class, [
              'label' => 'Mot de passe'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function createForm(EntityInterface $formData, array $options = []): FormInterface
    {
      return $this->formFactory->create(LoginType::class, $formData, $options);
    }
}
