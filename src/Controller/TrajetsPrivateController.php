<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Trajets;


class TrajetsPrivateController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/trajets/private', name: 'app_trajets_private')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();
        $trajetsRepository = $this->entityManager->getRepository(Trajets::class);
        $trajets = $trajetsRepository->findBy(['public' => false]);
        return $this->render('trajets_private/index.html.twig', [
            'trajets' => $trajets,
            'user' => $user,
        ]);
    }
}
