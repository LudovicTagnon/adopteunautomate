<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use App\Entity\Villes;
use App\Repository\UtilisateursRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;
use Symfony\Component\Security\Core\Security;

#[AsEntityAutocompleteField]
class VillesDepartAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
            $resolver->setDefaults([
            'class' => Villes::class,
            'multiple' => false,
            'required' => false,
            'label' => 'Ville de départ:',
            'choice_label' => function(Villes $ville) {
                return sprintf('%s', $ville->getnomVille());
            },
            'mapped' => false,
            //'query_builder' => function (UtilisateursRepository $repository) use ($user)
            //'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}

?>