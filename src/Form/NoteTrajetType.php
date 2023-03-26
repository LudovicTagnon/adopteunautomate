<?php

namespace App\Form;
use App\Entity\Note;
use App\Entity\Utilisateurs;
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
            ->add('Trajet_id', EntityType::class, [
                'class' => Trajets::class,
                'choice_label' => function (Trajets $trajet) {
                    return sprintf('%s -> %s', $trajet->getDemarreA()->getNomVille(), $trajet->getArriveA()->getNomVille());
                },
                'choices' => $options['trajets'],
                'label' => 'Sélectionnez un trajet :',
                'placeholder' => 'Choisissez un trajet',
                'property_path' => 'trajet',
            ])
            ->add('note', null, [
                'label' => 'Note',
            ])
            ->add('commentaire', null, [
                'label' => 'Commentaire',
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
            'trajets' => [],
        ]);
        $resolver->setAllowedTypes('trajets', 'array');
    }
}
