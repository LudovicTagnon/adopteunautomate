<?php

namespace App\Controller;

use App\Entity\Adopte;
use App\Entity\EstAccepte;
use App\Entity\Trajets;
use App\Entity\Utilisateurs;
use App\Repository\AdopteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\RequestStack;


class AdopteController extends AbstractController
{
    #[Route("/trajet/adopter/{trajetId}/{utilisateurId}", name:"app_trajet.adopter_trajet")]
    public function adopterUnTrajet(EntityManagerInterface $manager, int $trajetId, int $utilisateurId,NotificationService $notificationService,RequestStack $requestStack): Response
    {
        $trajet = $manager->getRepository(Trajets::class)->find($trajetId);
        $utilisateur = $manager->getRepository(Utilisateurs::class)->find($utilisateurId);

        if (!$trajet || !$utilisateur) {
            throw $this->createNotFoundException('Trajet ou utilisateur introuvable.');
        }

        $adopte = new Adopte();
        $adopte->setTrajet($trajet);
        $adopte->setUtilisateur($utilisateur);

        $manager->persist($adopte);
        $manager->flush();
        $notificationService->addNotificationAdopteTrajet("Vous avez adopté un trajet !", $utilisateur,$trajet); //notification
        $this->addFlash('success', "Vous avez bien adopté le trajet de : ". $trajet->getDemarreA()." vers ".$trajet->getArriveA());
        // On redirige l'utilisateur à la page où il était
        $previousUrl = $requestStack->getCurrentRequest()->headers->get('Referer');
        return $this->redirect($previousUrl);
        //return new Response('L\'utilisateur ' . $utilisateur->getNom() . ' a adopté le trajet pour : ' . $trajet->getArriveA());//à revoir la redirection
    }

    #[Route("/trajet/abandonner/{trajetId}/{utilisateurId}", name:"app_trajet.abandonner_trajet")]
    public function abandonnerTrajet(AdopteRepository $adopteRepository, EntityManagerInterface $manager, int $trajetId, int $utilisateurId,RequestStack $requestStack,NotificationService $notificationService): Response
    {
        dump($trajetId);
        $trajet = $manager->getRepository(Trajets::class)->find($trajetId);
        $adopte = $adopteRepository->findOneBy(['trajet' => $trajetId, 'utilisateur' => $utilisateurId]);
        $utilisateur = $this->getUser();

        dump($adopte);

        if (!$adopte) {
            $estAccepteRepository = $manager->getRepository(EstAccepte::class);
            $estAccepte = $estAccepteRepository->findOneBy(['trajet' => $trajetId, 'utilisateur' => $utilisateurId]);
            if ($estAccepte) {
                $manager->remove($estAccepte);
                $manager->flush();
            }
        }
        
        try {
            $manager->remove($adopte);
            $manager->flush();
        } catch (\Exception $e) {
        }
        $adopteRepository = $manager->getRepository(Adopte::class);
        $adopte = $adopteRepository->findOneBy(['utilisateur' => $utilisateur, 'trajet' => $trajet]);

        if ($adopte) {
            $manager->remove($adopte);
            $manager->flush();
        }
        $estAccepte = $manager->getRepository(EstAccepte::class)->findOneBy(['utilisateur' => $utilisateurId, 'trajet' => $trajetId]);
        if ($estAccepte) {
            $manager->remove($estAccepte);
            $manager->flush();
        }
        $trajet->decrementNbPassagerCourant();
        $notificationService->addNotificationAbandonneTrajet("Vous avez abandonné un trajet !", $utilisateur,$trajet); //notification
        $this->addFlash('success', "Le trajet a bien été abandonné");
        // On redirige l'utilisateur à la page où il était
        $previousUrl = $requestStack->getCurrentRequest()->headers->get('Referer');
        return $this->redirect($previousUrl);
    }
}
?>