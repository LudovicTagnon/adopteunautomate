<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use App\Repository\UtilisateursRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class UtilisateursAutocompleteField extends AbstractType
{
    private UtilisateursRepository $utilisateursRepository;
    public function __construct(UtilisateursRepository $utilisateursRepository)
    {
        $this->utilisateursRepository = $utilisateursRepository;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Utilisateurs::class,
            'placeholder' => 'Choose a Utilisateurs',
            'choices' => $this->utilisateursRepository->findAll(),
            'choice_label' => 'nom',
            'mapped' => false,
            //'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
