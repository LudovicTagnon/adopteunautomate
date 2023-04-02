<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Trajets;
use App\Entity\Utilisateurs;
use App\Repository\UtilisateursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/save_rating', name: 'save_rating', methods: ['POST'])]
    public function saveRating(Request $request, TrajetsRepository $trajetsRepository, UtilisateursRepository $utilisateursRepository): JsonResponse
    {
        dump($request->getMethod()); // Log the request method

        try {
            $data = json_decode($request->getContent(), true);

            dump($data);
            if (!isset($data['trajet_id'], $data['participant_id'], $data['note'])) {
                return new JsonResponse(['success' => false, 'message' => 'Invalid data received.'], 400);
            }
            $participantId = is_array($data['participant_id']) ? reset($data['participant_id']) : $data['participant_id'];

            var_dump($data);

            $trajet = $trajetsRepository->find($data['trajet_id']);
            if (!$trajet) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Invalid trajet ID',
                ], 400);
            }
            $participant = $utilisateursRepository->find($data['participant_id']);
            if (!$participant) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Invalid participant ID',
                ], 400);
            }

            // Check if the rating already exists
            $existingRating = $this->entityManager->getRepository(Note::class)->findOneBy([
                'trajet' => $trajet,
                'utilisateur' => $participant
            ]);

            if ($existingRating) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'You have already submitted a rating for this participant.'
                ], 409); // Conflict
            }

            if ($trajet && $participant) {
                $note = new Note();
                $note->setTrajet($trajet);
                $note->setUtilisateur($participant);
                $note->setNote($data['note']);

                $entityManager = $this->entityManager;
                $entityManager->persist($note);
                $entityManager->flush();

                return new JsonResponse(['success' => true]);
            } else {
                return new JsonResponse(['success' => false], 400);
            }
        } catch (\Throwable $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ], 500);
        }
    }


}
