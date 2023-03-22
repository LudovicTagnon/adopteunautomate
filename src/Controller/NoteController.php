<?php

namespace App\Controller;

use App\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\NoteTrajetType;
use Symfony\Component\HttpFoundation\Request;

class NoteController extends AbstractController
{
    #[Route('/notes', name: 'notes')]
    public function index(Request $request): Response
    {

        $form = $this->createForm(NoteTrajetType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trajet = $form->get('trajet')->getData();
        }

        return $this->render('notes/index.html.twig', [
            'controller_name' => 'NoteController',
            'form' => $form->createView(),
        ]);
    }
}