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
        if (!$request->query->has('id')) {
            return new Response('Missing parameter "id"', 400);
        }

        $trajetId = $request->query->get('id');

        $trajet = $this->entityManager->getRepository(Trajets::class)->find($trajetId);
        if (!$trajet) {
            return new Response('Trajet not found', 404);
        }

        $participants = $trajet->getParticipants();

        return $this->render('notes/participants.html.twig', [
            'participants' => $participants,
        ]);
    }

}

