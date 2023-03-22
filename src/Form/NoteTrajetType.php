<?php

namespace App\Form;

use App\Entity\Trajets;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteTrajetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('trajet', EntityType::class, [
                'class' => Trajets::class,
                'choice_label' => function (Trajets $trajet) {
                    return sprintf('%s -> %s', $trajet->getDemarreA()->getNomVille(), $trajet->getArriveA()->getNomVille());
                },
                'label' => 'Sélectionnez un trajet :',
                'placeholder' => 'Choisissez un trajet',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
