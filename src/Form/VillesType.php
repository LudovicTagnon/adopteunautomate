<?php

namespace App\Form;

use App\Entity\Villes;
use App\Repository\VillesRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class VillesType extends AbstractType
{
    private VillesRepository $villesRepository;

    public function __construct(VillesRepository $villesRepository)
    {
        $this->villesRepository = $villesRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $cities = $this->villesRepository->findAll();

        $builder
            ->add('nom_ville', ChoiceType::class,  [
                'label' => ' ',
                'choices' => $cities,
                'choice_label' => 'nom_ville',
                'choice_value' => 'id',
                'placeholder' => 'Sélectionner une ville',
                'autocomplete' => true,
                ])
            //->add('CP')          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Villes::class,
        ]);
    }
}
