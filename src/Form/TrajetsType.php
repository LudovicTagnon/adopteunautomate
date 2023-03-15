<?php

namespace App\Form;

use App\Entity\Villes;
use App\Entity\Trajets;
use ConvertirFormatDate;
use App\Entity\Utilisateurs;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Component\Validator\Context\ExecutionContextInterface;

// ajout en ligne de commande: composer require doctrine/doctrine-bundle

class TrajetsType extends AbstractType
{
    private UtilisateursRepository $utilisateursRepository;
    private $security;

    public function __construct(UtilisateursRepository $utilisateursRepository, Security $security)
    {
        //$this->utilisateursRepository = $utilisateursRepository;
       // $this->security = $security;
    }

    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //$user = $this->security->getUser();
        $tomorrow = new \DateTime('tomorrow');
        $demain = new \DateTime('+24 hours');
        $user = new Utilisateurs();

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

            ->add('public')
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

    //Verification si les dates sont valides (dateDepart < date)
    public function validateDates($value,ExecutionContextInterface $context, $payload)
    {
        $dateDepart = $context->getRoot()->get('T_depart')->getData();

        if($value < $dateDepart){
            $context->buildViolation('La date doit être postérieure à la date de départ')
                ->atPath('T_arrivee')
                ->addViolation();
        }
    }

    //Verifications trajet meme jour 
    public function validateJour(EntityManagerInterface $manager,ExecutionContextInterface $context){
        $user = $this->security->getUser();
        $existingvoyage = $manager->getRepository(Trajets::class)->findBy([
            'publie' => $user,
        ]);
        $dateDepart = $context->getRoot()->get('T_depart')->getData();
        foreach($existingvoyage as $trip){
            if($dateDepart->getTimestamp() <= $trip->getTArrivee()->getTimestamp()){
                $context->buildViolation('La date doit être postérieure à la date de départ')
                ->atPath('T_arrivee')
                ->addViolation();
            }
        }
    }
}
