<?php

namespace App\Form;

use App\Entity\Trajets;
use App\Entity\Groupes;
use App\Repository\GroupesRepository;
use App\Entity\Utilisateurs;
use App\Entity\Villes;
use ConvertirFormatDate;
use Doctrine\DBAL\Types\BooleanType;

use Symfony\Component\Form\AbstractType;
use App\Repository\UtilisateursRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
// use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeImmutableType;
use Symfony\Component\Validator\Constraints\GreaterThan;

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
        $nowPlus24h = new \DateTime();
        $nowPlus24h->modify('+24 hours');
        $user = new Utilisateurs();
        $userConnected = $this->security->getUser();

        $builder
            //->add('etat')
            ->add('T_depart', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Jour et heure de départ * :   ',
                'attr' => [
                    'min' => $nowPlus24h->format('Y-m-d\TH:i')
                ],
                'constraints' => [
                    new GreaterThan('now +24 hours')
                ]
            ])
            ->add('T_arrivee', DateTimeType::class, [
                'widget' => 'single_text',
                'required'   => false,
                'label' => 'Jour et heure d\'arrivée    :      ',
                'attr' => [
                    'min' => $nowPlus24h->format('Y-m-d\TH:i')
                ],
                'constraints' => [
                    new GreaterThan('now +24 hours')
                ]
            ]) 
            ->add('demarrea', VillesDepartAutocompleteField::class)
            ->add('arrivea', VillesDepartAutocompleteField::class)




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
                'label' => 'Trajet public ou privé * :   '
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
