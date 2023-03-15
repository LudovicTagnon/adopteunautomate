<?php

namespace App\Controller;

use App\Entity\EstAccepte;
use App\Entity\Adopte;
use App\Entity\Trajets;
use App\Entity\Utilisateurs;
use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\RequestStack;

class EstAccepteController extends AbstractController
{

    #[Route("/notification/estAccepte/{trajetId}/{utilisateurId}/{notificationId}", name:"app.estAccepte")]
    public function accepterUnUtilisateur(EntityManagerInterface $manager, int $trajetId, int $utilisateurId,NotificationService $notificationService,int $notificationId): Response
    {
        $trajet = $manager->getRepository(Trajets::class)->find($trajetId);
        $notification = $manager->getRepository(Notification::class)->find($notificationId);
        if (!$trajet) {
            throw $this->createNotFoundException('Trajet introuvable.');
        }

        $utilisateur = $manager->getRepository(Utilisateurs::class)->find($utilisateurId);

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }
        $estAccepte = $manager->getRepository(EstAccepte::class)->findOneBy(['trajet' => $trajet, 'utilisateur' => $utilisateur]);
        if ($estAccepte) {
            $this->addFlash('warning', "L'utilisateur ".$utilisateur->getNom(). " a déjà été accepté pour ce trajet.");
            return $this->redirectToRoute('app_supprimer_notif', ['id' => $notification->getId()]);
        }
        $accepte = new EstAccepte();
        $accepte->setTrajet($trajet);
        $accepte->setUtilisateur($utilisateur);
        $trajet->incrementNbPassagerCourant();
        $manager->persist($accepte);
        $manager->persist($trajet);
        $manager->flush();

        $notificationService->addNotificationAccepteTrajet("Vous avez été accepté au trajet : ".$trajet->__toString(), $utilisateur); //notification
        $this->addFlash('success', "L'utilisateur ".$utilisateur->getNom(). " a été accepté pour le trajet de : ". $trajet->getDemarreA()." vers ".$trajet->getArriveA());
        // On redirige l'utilisateur à la page où il était en supprimant la notification
        return $this->redirectToRoute('app_supprimer_notif', ['id' => $notification->getId()]);
        //return new Response('L\'utilisateur ' . $utilisateur->getNom() . ' a adopté le trajet pour : ' . $trajet->getArriveA());//à revoir la redirection
    }

    
    #[Route("/notification/estRefuse/{trajetId}/{utilisateurId}/{notificationId}", name:"app.estRefuse")]
    public function refuserUnUtilisateur(EntityManagerInterface $manager, int $trajetId, int $utilisateurId,int $notificationId,NotificationService $notificationService): Response
    {
        $trajet = $manager->getRepository(Trajets::class)->find($trajetId);
        $notification = $manager->getRepository(Notification::class)->find($notificationId);

        if (!$trajet) {
            throw $this->createNotFoundException('Trajet introuvable.');
        }

        $utilisateur = $manager->getRepository(Utilisateurs::class)->find($utilisateurId);

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }
        $estAccepte = $manager->getRepository(EstAccepte::class)->findOneBy(['trajet' => $trajet, 'utilisateur' => $utilisateur]);
        if (!$estAccepte) {
            $this->addFlash('warning', "L'utilisateur ".$utilisateur->getNom(). " a déjà été supprimé pour ce trajet.");
            return $this->redirectToRoute('app_supprimer_notif', ['id' => $notification->getId()]);
        }
        $notificationService->addNotificationRefuseTrajet("Vous avez été refusé au trajet : ".$trajet->__toString(), $utilisateur); //notification
        $this->addFlash('success', "L'utilisateur ".$utilisateur->getNom(). " a été refusé pour le trajet de : ". $trajet->getDemarreA()." vers ".$trajet->getArriveA());
        // Retirer l'utilisateur refusé de la liste des utilisateurs adoptant ce trajet
        $adopteRepository = $manager->getRepository(Adopte::class);
        $adopte = $adopteRepository->findOneBy(['utilisateur' => $utilisateur, 'trajet' => $trajet]);

        if ($adopte) {
            $manager->remove($adopte);
            $manager->flush();
        }
        // On redirige l'utilisateur à la page où il était en supprimant la notification
        return $this->redirectToRoute('app_supprimer_notif', ['id' => $notification->getId()]);
    }

}
