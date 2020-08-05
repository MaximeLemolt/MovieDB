<?php

namespace App\Form\Type;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'empty_data' => '',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'empty_data' => '',
                // Champ non mappé automatiquement sur l'entité
                'mapped' => false,
            ])
            ->add('userRoles', EntityType::class, [
                'label' => 'Choisissez le rôle de l\'utilisateur',
                'class' => Role::class,
                'choice_name' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ]
        ]);
    }
}
