<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[

            ])
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
                'label' => 'Mot de passe *',
                'label_attr' => ['class' => 'required-field'],
            ])
            ->add('nom', TextType::class, [
                'required' => true,
                'attr' => ['autocomplete' => 'nom'],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-z\s\-]+$/',
                        'message' => 'Le nom ne doit contenir que des caractères alphabétiques, des espaces et des tirets.',
                    ]),
                ],
                'label_attr' => ['class' => 'required-field'],
            ])
            ->add('prenom', TextType::class, [
                'required' => true,
                'attr' => ['autocomplete' => 'prenom'],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-z\s\-]+$/',
                        'message' => 'Le prenom ne doit contenir que des caractères alphabétiques, des espaces et des tirets.',
                    ]),
                ],
                'label_attr' => ['class' => 'required-field'],
            ])
            ->add('num_tel', TextType::class, [
                'required' => true,
                'attr' => ['autocomplete' => 'num_tel'],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^0[1-9](\d{2}){4}$/',
                        'message' => 'Le numéro de téléphone doit être un numéro de téléphone français.',
                    ]),
                ],
                'label_attr' => ['class' => 'required-field'],
            ])
            ->add('vehicule', CheckboxType::class, [
                'required' => false,
                'attr' => ['autocomplete' => 'vehicule'],
                'label_attr' => ['class' => 'required-field'],
            ])
            ->add('genre', ChoiceType::class, [
                'choices' => [
                    'Male' => 'homme',
                    'Female' => 'femme',
                    'Other' => 'autre',
                ],
                'required' => true,
                'label_attr' => ['class' => 'required-field'],
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
