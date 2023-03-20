<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Trajets;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\EstAccepte;
class TrajetsPublicController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/public', name: 'app_trajets_publics')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();
        $trajetsRepository = $this->entityManager->getRepository(Trajets::class);
        $trajets = $trajetsRepository->findBy(['public' => true]);
        $estAccepteRepository = $this->entityManager->getRepository(EstAccepte::class);
        $estAccepte = $estAccepteRepository->findAll();
        return $this->render('trajets_public/index.html.twig', [
            'trajets' => $trajets,
            'user' => $user,
            'estAccepte' => $estAccepte,
        ]);
    }
}
