<?php

namespace App\Form;

use App\Entity\Trajets;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class TrajetsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('etat')
            ->add('T_depart')
            ->add('T_arrivee')
            ->add('prix')
            ->add('nb_passager_max')
            ->add('nb_passager_courant')
            ->add('public')
            ->add('renseignement')
           // ->add('publie', UtilisateursType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trajets::class,
        ]);
    }
}
