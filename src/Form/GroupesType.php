<?php

namespace App\Form;

use Assert\Length;
use App\Entity\Groupes;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use App\Repository\UtilisateursRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GroupesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // pb avec le caratère composite de "utilisateur" -> on le supprime
        // on crée un groupe sans utilisateur
        // mise en forme à 35min/1h16 de CRUD
        $builder
            ->add('nom_groupe' , TextType::class, [
                'attr' => [
                    'class' => 'form_control',
                    'minlength' => '1',
                    'maxlength' => '50'
                ],
                'label' => 'Nom du groupe   :',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Length( ['min' => 1, 'max' => 50] ),
                    new Assert\NotBlank(message: 'This value should not be blank')
                ]

            ])

            // problème rencontré: les nom des forms sont liés aux entités par
            // symfony .
            /*
            -> add ('utilisateurs', EntityType::class,
             [
                */
            //'class' => UserType::class
            //'class' => UtilisateuUrs::class
            /*
            ,
            'query_builder' => function (UtilisateursRepository $er){
                return $er ->createQueryBuilder('u')
                 ->orderBy('u.mail', 'ASC');
            },
            'label' => "Les utilisateurs",
            'label_attr' => [
                'class' => 'form-label mt-4'
            ],
            'choice_label' => 'mail',
            'multiple' => true,
            'expanded' => true */
            /*
    ]
    )*/

            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4'
                ],
                'label' => 'Créer un groupe'
            ]);
        //->add('utilisateurs')

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Groupes::class,
        ]);
    }
}
