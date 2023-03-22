<?php

namespace App\Controller;

use App\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    #[Route('/notes', name: 'notes')]
    public function index(): Response
    {
        return $this->render('notes/index.html.twig', [
            'controller_name' => 'NoteController',
        ]);
    }
}