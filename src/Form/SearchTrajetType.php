<?php

namespace App\Form;

use App\Entity\Villes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
class SearchTrajetType extends AbstractType
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('from', TextType::class, [
                'required' => true,
                'label' => 'Ville de départ:',
                'attr' => [
                    'autocomplete' => 'off', // Disable browser autocomplete
                    'class' => 'form-control', // Add bootstrap class for styling
                ],
                'autocomplete_url' => $this->urlGenerator->generate('app_autocomplete_villes'),
            ])
            ->add('to', TextType::class, [
                'required' => true,
                'label' => 'Ville d\'arrivée:',
                'attr' => [
                    'autocomplete' => 'off', // Disable browser autocomplete
                    'class' => 'form-control', // Add bootstrap class for styling
                ],
                'autocomplete_url' => $this->urlGenerator->generate('app_autocomplete_villes'),
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Villes::class,
        ]);
    }
}
