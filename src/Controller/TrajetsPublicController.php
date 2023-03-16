<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Trajets;
use Doctrine\ORM\EntityManagerInterface;


class TrajetsPublicController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/trajets/public', name: 'app_trajets_public')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();
        $trajetsRepository = $this->entityManager->getRepository(Trajets::class);
        $trajets = $trajetsRepository->findBy(['public' => true]);
        return $this->render('trajets_public/index.html.twig', [
            'trajets' => $trajets,
            'user' => $user,
        ]);
    }
}
