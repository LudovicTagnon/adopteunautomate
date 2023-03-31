<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Trajets;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\NoteTrajetType;
use App\Repository\TrajetsRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FormService;

class NoteController extends AbstractController
{
    private $formService;
    private $entityManager;
    private $trajetsRepository;

    public function __construct(EntityManagerInterface $entityManager, TrajetsRepository $trajetsRepository, FormService $formService)
    {
        $this->entityManager = $entityManager;
        $this->trajetsRepository = $trajetsRepository;
        $this->formService = $formService;
    }

    #[Route('/notes', name: 'notes')]
    public function index(Request $request): Response
    {
        $trajets = $this->trajetsRepository->findAll();

        $participantForm = $this->createFormBuilder()
            ->add('Trajet_id', EntityType::class, [
                'class' => Trajets::class,
                'choice_label' => function (Trajets $trajet) {
                    return sprintf('%s -> %s', $trajet->getDemarreA()->getNomVille(),
                        $trajet->getArriveA()->getNomVille());
                },
                'choices' => $trajets,
                'label' => 'Sélectionnez un trajet :',
                'placeholder' => 'Choisissez un trajet',
            ])
            ->getForm();

        $noteForm = $this->createForm(NoteTrajetType::class, null, [
            'trajets' => $trajets,
        ]);

        $participantForm->handleRequest($request);
        $noteForm->handleRequest($request);

        $participants = [];
        $selectedTrajet = null;
        if ($participantForm->isSubmitted() && $participantForm->isValid()) {
            $trajetId = $participantForm->get('Trajet_id')->getData()->getId();
            $participants = $this->entityManager
                ->getRepository(Utilisateurs::class)
                ->findParticipantsByTrajet($trajetId);
            $selectedTrajet = $this->entityManager
                ->getRepository(Trajets::class)
                ->find($trajetId);
            $noteForm = $this->createForm(NoteTrajetType::class, null, [
                'trajets' => $trajets,
                'participants' => $participants,
            ]);
            $noteForm->handleRequest($request);
        }

        if ($noteForm->isSubmitted() && $noteForm->isValid()) {
            // Récupérer l'entité Note à partir du formulaire
            $note = $noteForm->getData();

            // Ajouter l'utilisateur actuel en tant qu'auteur de la note
            $user = $this->getUser();
            $note->setAuteur($user);

            // Enregistrer la note dans la base de données
            $this->entityManager->persist($note);
            $this->entityManager->flush();

            // Rediriger vers la page souhaitée après l'enregistrement de la note
            return $this->redirectToRoute('notes');
        }

        return $this->render('notes/new.html.twig', [
            'controller_name' => 'NoteController',
            'participantForm' => $participantForm->createView(),
            'noteForm' => $noteForm->createView(),
            'participants' => $participants,
            'trajets' => $trajets,
            'formService' => $this->formService,
            'selectedTrajet' => $selectedTrajet,
        ]);
    }

    #[Route('/note_participants/{trajetId}', name: 'note_participants', methods: ['GET'])]
    public function loadParticipants(Trajets $trajetId): Response
    {
        $participants = $this->entityManager
            ->getRepository(Utilisateurs::class)
            ->findParticipantsByTrajet($trajetId);

        $data = [];
        foreach ($participants as $participant) {
            $data[] = [
                'id' => $participant->getId(),
                'nom' => $participant->getNom(),
                'prenom' => $participant->getPrenom(),
            ];
        }

        return $this->json($data);
    }

}
