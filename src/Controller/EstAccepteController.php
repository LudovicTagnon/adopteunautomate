<?php

namespace App\Controller;

use App\Entity\EstAccepte;
use App\Entity\Trajets;
use App\Entity\Utilisateurs;
use App\Repository\EstAccepteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\RequestStack;

class EstAccepteController extends AbstractController
{

    #[Route("/notification/estAccepte/{trajetId}/{utilisateurId}", name:"app.estAccepte")]
    public function accepterUnUtilisateur(EntityManagerInterface $manager, int $trajetId, int $utilisateurId,NotificationService $notificationService,RequestStack $requestStack): Response
    {
        $trajet = $manager->getRepository(Trajets::class)->find($trajetId);
        $utilisateur = $manager->getRepository(Utilisateurs::class)->find($utilisateurId);
        if (!$trajet) {
            throw $this->createNotFoundException('Trajet introuvable.');
        }

        $utilisateur = $manager->getRepository(Utilisateurs::class)->find($utilisateurId);

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }
        $accepte = new EstAccepte();
        $accepte->setTrajet($trajet);
        $accepte->setUtilisateur($utilisateur);

        $manager->persist($accepte);
        $manager->flush();
        //$notificationService->addNotificationAdopteTrajet("Vous avez adopté un trajet !", $utilisateur,$trajet); //notification
        $this->addFlash('success', "L'utilisateur ".$utilisateur->getNom(). " a été accepté pour le trajet de : ". $trajet->getDemarreA()." vers ".$trajet->getArriveA());
        // On redirige l'utilisateur à la page où il était en supprimant la notification
        return $this->redirectToRoute('app_supprimer_notif');//TODO:changer ici
        //return new Response('L\'utilisateur ' . $utilisateur->getNom() . ' a adopté le trajet pour : ' . $trajet->getArriveA());//à revoir la redirection
    }

    
    #[Route("/notification/estRefuse/{trajetId}/{utilisateurId}", name:"app.estRefuse")]
    public function refuserUnUtilisateur(EntityManagerInterface $manager, int $trajetId, int $utilisateurId,NotificationService $notificationService,RequestStack $requestStack): Response
    {
        $trajet = $manager->getRepository(Trajets::class)->find($trajetId);

        if (!$trajet) {
            throw $this->createNotFoundException('Trajet introuvable.');
        }

        $utilisateur = $manager->getRepository(Utilisateurs::class)->find($utilisateurId);

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }
        //TODO:envoyer les notifs
        $this->addFlash('success', "L'utilisateur ".$utilisateur->getNom(). " a été refusé pour le trajet de : ". $trajet->getDemarreA()." vers ".$trajet->getArriveA());
        //TODO:retirer l'utilisateur refusé de adopte ??
        // On redirige l'utilisateur à la page où il était en supprimant la notification
        return $this->redirectToRoute('app_supprimer_notif');//TODO:changer ici
    }

}
