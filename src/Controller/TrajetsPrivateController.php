<?php

namespace App\Controller;

use App\Entity\EstAccepte;
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
    #[Route('/trajets/mes_trajets/prives', name: 'app_trajets_prives')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser(); //on récupère l'utilisateur connecté
        $trajetsRepository = $this->entityManager->getRepository(Trajets::class);
        $groupesRepository = $this->entityManager->getRepository(Groupes::class);
        $estAccepteRepository = $this->entityManager->getRepository(EstAccepte::class);
        $lesgroupes = $groupesRepository->findAll();
        $trajets = $trajetsRepository->findBy(['public' => false]); //on récupère les trajets privés
        $groupesUser = $user->getGroupes();
        $estAccepte = $estAccepteRepository->findAll();
        if (count($lesgroupes) == 0) { //si aucun groupe pas de trajets privés
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
                'estAccepte' => $estAccepte,
            ]);
        }
    }
}
