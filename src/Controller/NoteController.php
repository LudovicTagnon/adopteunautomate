<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Trajets;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\Curl\Util;
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
        $form = $this->createForm(NoteTrajetType::class, null, [
            'trajets' => $trajets,
        ]);
        $form->handleRequest($request);

        $participants = [];
        $selectedTrajet = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $trajetId = $form->get('Trajet_id')->getData()->getId();
            $participants = $this->entityManager
                ->getRepository(Utilisateurs::class)
                ->findParticipantsByTrajet($trajetId);
            $selectedTrajet = $this->entityManager
                ->getRepository(Trajets::class)
                ->find($trajetId);
        }

        return $this->render('notes/new.html.twig', [
            'controller_name' => 'NoteController',
            'form' => $form->createView(),
            'participants' => $participants,
            'trajets' => $trajets,
            'formService' => $this->formService,
            'selectedTrajet' => $selectedTrajet,
        ]);
    }




    #[Route('/note_participants', name: 'note_participants', methods: ['GET'])]
    public function loadParticipants(Request $request): Response
    {
        $trajets = $this->trajetsRepository->findAll();
        $form = $this->createForm(NoteTrajetType::class, null, [
            'trajets' => $trajets,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trajetId = $form->get('Trajet_id')->getData();
            if (!$trajetId) {
                // No Trajet selected
                $this->addFlash('error', 'Please select a Trajet');
                return $this->redirectToRoute('notes');
            }

            $trajet = $this->entityManager
                ->getRepository(Trajets::class)
                ->find($trajetId);

            $participants = $this->entityManager
                ->getRepository(Utilisateurs::class)
                ->findParticipantsByTrajet($trajetId);

            return $this->render('notes/participants.html.twig', [
                'participants' => $participants,
                'form' => $form->createView(),
            ]);
        }
        return $this->render('notes/participants.html.twig', [
            'form' => $form->createView(),
            'participants' => [],
        ]);
    }




}

