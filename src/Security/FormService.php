<?php
// src/Service/FormService.php
namespace App\Service;

use App\Entity\Utilisateurs;
use App\Entity\Trajets;
use App\Entity\Note;
use App\Form\NoteTrajetType;
use Symfony\Component\Form\FormFactoryInterface;

class FormService
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
    $this->formFactory = $formFactory;
    }

    public function createNoteForm(Utilisateurs $participant, Trajets $trajet)
    {
    $note = new Note();
    $note->setTrajet($trajet);
    $note->setUtilisateur($participant);

        $form = $this->formFactory->create(NoteTrajetType::class, $note);
        return $form->createView();
    }
}
