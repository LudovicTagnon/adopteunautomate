<?php
namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',EmailType::class)
            ->add('prenom',TextType::class)
            ->add('nom',TextType::class,[
                'required' => true,
                'attr' => ['autocomplete' => 'nom'],
            ])
            ->add('num_tel',TextType::class,[
                'required' => true,
                'attr' => ['autocomplete' => 'num_tel'],
            ])
            ->add('vehicule',CheckboxType::class,[
                'required' => false,
                'attr' => ['autocomplete' => 'vehicule'],
            ])
            ->add('genre', ChoiceType::class, [
                'choices' => [
                    'Male' => 'homme',
                    'Female' => 'femme',
                    'Other' => 'autre',
                ],
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}

