<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Trajets;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\NoteTrajetType;
use App\Repository\TrajetsRepository;
use Symfony\Component\HttpFoundation\Request;

class NoteController extends AbstractController
{
    private $entityManager;
    private $trajetsRepository;
    public function __construct(EntityManagerInterface $entityManager, TrajetsRepository $trajetsRepository)
    {
        $this->entityManager = $entityManager;
        $this->trajetsRepository = $trajetsRepository;
    }

    #[Route('/notes', name: 'notes')]
    public function index(Request $request): Response
    {
        $participants = [];
        $trajets = $this->trajetsRepository->findAll();
        $form = $this->createForm(NoteTrajetType::class, null, [
            'trajets' => $trajets,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Trajets $trajet */
            $trajet = $form->get('Trajet_id')->getData();
            $participants = $trajet->getParticipants();
        }

        return $this->render('notes/new.html.twig', [
            'controller_name' => 'NoteController',
            'form' => $form->createView(),
            'participants' => $participants,
            'trajets' => $trajets,
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

            $participants = $trajet->getParticipants();
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

