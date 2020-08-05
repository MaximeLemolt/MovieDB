<?php

namespace App\Form\Type;

use App\Entity\Job;
use App\Entity\Department;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du job',
                'empty_data' => '',
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class, // Avec null, cette info ne serait pas utile
                'choice_label' => 'name',
                'placeholder' => 'Choisissez un departement...',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
