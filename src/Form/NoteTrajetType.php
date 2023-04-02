<?php

namespace App\Form;

use App\Entity\Note;
use App\Entity\Trajets;
use App\Entity\Utilisateurs;
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
            ->add('participant_id', EntityType::class, [
                'class' => Utilisateurs::class,
                'choice_label' => function (Utilisateurs $participant) {
                    return sprintf('%s %s', $participant->getNom(), $participant->getPrenom());
                },
                'choices' => $options['participants'],
                'label' => 'Sélectionnez un participant :',
                'placeholder' => 'Choisissez un participant',
                'property_path' => 'utilisateur',
            ])

            ->add('note', null, [
                'label' => 'Note',
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
            'trajets' => [],
            'participants' => [],
        ]);
        $resolver->setAllowedTypes('trajets', 'array');
        $resolver->setAllowedTypes('participants', 'array');
    }

    private function getParticipantChoices($participants)
    {
        $choices = [];
        foreach ($participants as $participant) {
            $choices[$participant->getId()] = sprintf('%s %s', $participant->getNom(), $participant->getPrenom());
        }
        return $choices;
    }
}
