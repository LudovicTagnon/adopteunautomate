<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use App\Repository\UtilisateursRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;
use Symfony\Component\Security\Core\Security;

#[AsEntityAutocompleteField]
class UtilisateursAutocompleteField extends AbstractType
{
    private UtilisateursRepository $utilisateursRepository;
    private Security $security;
    public function __construct(UtilisateursRepository $utilisateursRepository,Security $security)
    {
        $this->utilisateursRepository = $utilisateursRepository;
        $this->security = $security;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
            $user = $this->security->getUser();
            $resolver->setDefaults([
            'class' => Utilisateurs::class,
            'placeholder' => 'Choose a Utilisateurs',
            'choice_label' => function(Utilisateurs $user) {
                return sprintf('%s %s (%s)', $user->getPrenom(), $user->getNom(), $user->getEmail());
            },
            'mapped' => false,
            'query_builder' => function (UtilisateursRepository $repository) use ($user) {
                // Exclude the current user from the list of options
                return $repository->createQueryBuilder('u')
                    ->where('u != :user')
                    ->setParameter('user', $user);
            },
            //'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
