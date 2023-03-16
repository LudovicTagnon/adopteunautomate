<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Trajets;
use App\Entity\Groupes;
use App\Entity\EstDans;
use App\Entity\Utilisateurs;


class TrajetsPrivateController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/private', name: 'app_trajets_prive')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser(); //on récupère l'utilisateur connecté
        $trajetsRepository = $this->entityManager->getRepository(Trajets::class);
        $groupesRepository = $this->entityManager->getRepository(Groupes::class);
        $lesgroupes = $groupesRepository->findAll();
        $trajets = $trajetsRepository->findBy(['public' => false]); //on récupère les trajets privés
        $groupesUser = $user->getGroupes();
        if (count($lesgroupes) == 0) {
            return $this->render('trajets_private/index.html.twig', [
                'trajets' => $trajets,
                'user' => $user,
                'groupes' => null,
            ]);
        }
        else{
            foreach ($trajets as $trajet) {
                $groupes = $trajet->getGroupes();
            }
            $estDans = array();
            $estDedans = false;
            foreach ($groupes as $groupe) {
                if ($groupe->estDansGroupes($user->getId())) {
                    $estDedans = true;
                }
                $estDans[] = $groupe->getUtilisateurs();
            }
            return $this->render('trajets_private/index.html.twig', [
                'trajets' => $trajets,
                'user' => $user,
                'estDans' => $estDans,
                'groupes' => $groupes,
                'estDedans' => $estDedans,
                'groupesUser' => $groupesUser,
            ]);
        }
    }
}
