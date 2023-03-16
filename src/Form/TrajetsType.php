<?php

namespace App\Form;

use App\Entity\Villes;
use App\Entity\Trajets;
use ConvertirFormatDate;
use App\Entity\Utilisateurs;
use Doctrine\DBAL\Types\BooleanType;

use Symfony\Component\Form\AbstractType;
use App\Repository\UtilisateursRepository;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Form\Extension\Core\Type\DateTimeImmutableType;


use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Validator\Constraints\NotBlank;

// ajout en ligne de commande: composer require doctrine/doctrine-bundle

class TrajetsType extends AbstractType
{
    private UtilisateursRepository $utilisateursRepository;
    private GroupesRepository $groupesRepository;
    private $security;

    public function __construct(UtilisateursRepository $utilisateursRepository, GroupesRepository $groupesRepository, Security $security)
    {
        //$this->utilisateursRepository = $utilisateursRepository;
       // $this->security = $security;
       $this->utilisateursRepository = $utilisateursRepository;
       $this->groupesRepository = $groupesRepository;
       $this->security = $security;
    }

    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //$user = $this->security->getUser();
        $tomorrow = new \DateTime('tomorrow');
        $demain = new \DateTime('+24 hours');
        $user = new Utilisateurs();
        $userConnected = $this->security->getUser();

        $builder
            //->add('etat')
            ->add('T_depart', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Jour et heure de départ * :   '
            ])
            ->add('T_arrivee', DateTimeType::class, [
                'widget' => 'single_text',
                'required'   => false,
                'label' => 'Jour et heure d\'arrivée    :      ',
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 'tomorrow',
                        'message' => 'Votre trajet doit commencer dans plus de 24h.'
                    ])
                ]
            ]) 
            ->add('demarrea', EntityType::class, [
                'class' => Villes::class,
                'choice_label' => 'nomVille',
                'label' => 'Ville de départ * :'
            ])
            ->add('arrivea', EntityType::class, [
                'class' => Villes::class,
                'choice_label' => 'nomVille',
                'label' => "Ville d''arrivée * :"
            ])




            /* ->add('demarrea', VillesType::class, [
              //  'mapped' => false, // Do not map this field to an entity property
                'label' => '  Ville de Départ:  '
              //'class' => 'App\Entity\Villes',
              //'choice_label' => 'demarrea',
              //'required' => true,
            ])

            ->add('arrivea', VillesType::class, [
             //   'mapped' => false, // Do not map this field to an entity property
                'label' => '   Ville d\' Arrivée :      '
            ]) */




            /*
            ->add('demarrea', EntityType::class, [
                'label' => 'Ville de départ * :   ',
                'class' => 'App\Entity\Villes'
               // 'widget' => 'single_text',
               // 'class' => Villes::class,
               // 'choice_label' => 'nom_ville'
                
            ])   
            ->add('arrivea', EntityType::class, [
                'label' => 'Ville d\'arrivee * :   ',
                'class' => 'App\Entity\Villes'
              //  'widget' => 'single_text',
               // 'class' => Villes::class,
               // 'choice_label' => 'nom_ville'
               
            ])  
            */ 

            ->add('prix', IntegerType::class,[
                'required'   => false,
                'label' => 'Prix par passager :     '
            ])
            ->add('nb_passager_max', IntegerType::class,[
                'label' => 'Nombre de places * :   '
            ])

            ->add('public', ChoiceType::class, [
                'choices' => [
                    ' Public ' => true,
                    ' Privé' => false,
                ],
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'label' => 'Trajet public ou privé :   '
            ])
            ->add('groupes', EntityType::class, [
                'class' => Groupes::class,
                'choices' => $this->groupesRepository->findBy(['createur' => $userConnected]),
                'choice_label' => 'nom_groupe',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label' => 'Groupes à inviter',
                'mapped' => false, // add this line

            ])
            
            //a ajouter la selection des groupes
            
            ->add('renseignement', TextareaType::class, [
                'required'   => false,
                'label' => 'Informations additionnelles :   '
            ])
           
        ;

        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trajets::class,
        ]);
    }
}
