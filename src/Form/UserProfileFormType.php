<?php
namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class UserProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',EmailType::class)
            ->add('prenom',TextType::class,[
                'required' => true,
                'attr' => ['autocomplete' => 'prenom'],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-z\s\-]+$/',
                        'message' => 'Le prénom ne doit contenir que des caractères alphabétiques, des espaces et des tirets.',
                    ]),
                ],
            ])
            ->add('nom',TextType::class,[
                'required' => true,
                'attr' => ['autocomplete' => 'nom'],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-z\s\-]+$/',
                        'message' => 'Le nom ne doit contenir que des caractères alphabétiques, des espaces et des tirets.',
                    ]),
                ],
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

