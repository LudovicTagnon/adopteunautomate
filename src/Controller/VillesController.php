<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class VillesController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/villes', name: 'app_villes')]
    public function index(): Response
    {
        return $this->render('villes/index.html.twig', [
            'controller_name' => 'VillesController',
        ]);
    }
    #[Route('/autocomplete/villes', name: 'app_autocomplete_villes')]
    public function autocomplete(Request $request): JsonResponse
    {
        $term = $request->query->get('term');
        
        $villes = $this->entityManager->getRepository(Ville::class)->findByTerm($term);
        
        $result = [];
        foreach ($villes as $ville) {
            $result[] = [
                'id' => $ville->getId(),
                'text' => $ville->getNom(),
            ];
        }
        
        return $this->json($result);
    }
}
