<?php

namespace App\Form;

use Assert\Length;
use App\Entity\Groupes;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use App\Repository\UtilisateursRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GroupesType extends AbstractType
{
    private UtilisateursRepository $utilisateursRepository;
    private $security;

    public function __construct(UtilisateursRepository $utilisateursRepository, Security $security)
    {
        $this->utilisateursRepository = $utilisateursRepository;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();

        //$utilisateursDisponibles = 
        //$utilisateursAjoutes = // récupérez les utilisateurs déjà ajoutés

        $builder
            ->add('nom_groupe' , TextType::class, [
                'attr' => [
                    'class' => 'form_control',
                    'minlength' => '1',
                    'maxlength' => '50'
                ],
                'label' => 'Nom du groupe*   :',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Length( ['min' => 1, 'max' => 50] ),
                    new Assert\NotBlank(message: 'Ce champs est obligatoire')
                ]

            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'form_control',
                    'minlength' => '0',
                    'maxlength' => '500',
                ],
                'label' => 'Description :',
                'label_attr' =>[
                    'class' => 'form-label mt-4'
                ],
                'required' => false
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Groupes::class,
            'user' => null,
        ]);
    }
}
