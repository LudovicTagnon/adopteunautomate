<?php

namespace App\Form;

use App\Entity\Trajets;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;

use App\Repository\UtilisateursRepository;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeImmutableType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;

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

        $builder
            //->add('etat')
            ->add('T_depart', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Jour et heure de départ * :   ',
                // début par défaut: lendemain de la saisie
                'data' => $tomorrow
            ])
           // ->add('T_depart')
            ->add('T_arrivee', DateTimeType::class, [
                'widget' => 'single_text',
                'required'   => false,
                'label' => 'Jour et heure d\'arrivée    :      '
                ])
            //->add('T_arrivee')    
            ->add('prix', IntegerType::class,[
                'required'   => false,
                'label' => 'Prix par passager :     '
            ])
            ->add('nb_passager_max', IntegerType::class,[
                'label' => 'Nombre de places * :   '
            ])
            //->add('nb_passager_courant')
            ->add('public')
            ->add('renseignement', TextareaType::class, [
                'required'   => false,
                'label' => 'Informations additionnelles :   '
            ])
           // ->add('publie', UtilisateursType::class)
           /*
           ->add('submit', SubmitType::class, [
            'attr' => [
                'class' => 'btn btn-primary mt-4'
            ],
            'label' => 'Créer le trajet'
             ])
             */

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trajets::class,
        ]);
    }
}
