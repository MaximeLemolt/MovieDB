<?php

namespace App\Form\Type;

use App\Entity\Genre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Définition du form (=ici les champs de l'entité que l'on souhaite gérer avec le form)
        $builder
            ->add('title', TextType::class, [
                // Si une chaine vide est passée, on envoie bien une chaine vide
                // De base, Symfony convertie une chaine vide en Null
                'empty_data' => '',
            ])

            // On pourrait utiliser null, et laisser symfony deviner quel type utiliser (ici: EntityType::class)
            // cependant, c'est moins précis et pourrait créer des bugs si Symfony devait fonctionner différement
            ->add('genres', EntityType::class, [
                'class' => Genre::class, // Avec null, cette info ne serait pas utile
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }
}