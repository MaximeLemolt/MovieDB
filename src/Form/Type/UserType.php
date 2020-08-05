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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'empty_data' => ''
            ])
            // On définit le champ Password une prémière fois ici, pour avoir le bon ordre des champs dans l'affichage
            // Ce champ sera réécrit dans la fonction onPreSetData
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'empty_data' => '',
            ])
            ->add('userRoles', EntityType::class, [
                'label' => 'Choisissez le rôle de l\'utilisateur',
                'class' => Role::class,
                'choice_name' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            // Ecouteur d'évènements
            // PRE_SET_DATA => Déclenché avant que les valeurs de l'entité ne soient passées au formulaire
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData'])
        ;
    }

    public function onPreSetData(FormEvent $event)
    {
        // dump($event);

        // On récupère le form
        $form = $event->getForm();

        // On récupère l'entité User courante contenue dans le form
        $user = $event->getData();

        // Si l'id du User est différent Null alors on est en modification
        if ($user->getId() !== null) {
            // On ajoute le champ 'password'
            $form->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'empty_data' => '',
                'mapped' => false,
            ]);
        }
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
