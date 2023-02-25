<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'required' => true,
                'attr' => ['autocomplete' => 'nom'],
            ])
            ->add('prenom', TextType::class, [
                'required' => true,
                'attr' => ['autocomplete' => 'prenom'],
            ])
            ->add('num_tel', TextType::class, [
                'required' => true,
                'attr' => ['autocomplete' => 'num_tel'],
            ])
            ->add('vehicule', CheckboxType::class, [
                'required' => false,
                'attr' => ['autocomplete' => 'vehicule'],
            ])
            ->add('genre', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Male' => 'male',
                    'Female' => 'female',
                    'Other' => 'other',
                ],
                'expanded' => true,
                'multiple' => false,

            ])
            ->add('autorisation_mail', CheckboxType::class, [
                'required' => false,
                'attr' => ['autocomplete' => 'autorisation_mail'],
            ])
            ->add('fichier_photo', FileType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['autocomplete' => 'fichier_photo'],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}
