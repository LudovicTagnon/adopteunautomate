<?php

namespace App\Controller;

use App\Entity\EstAccepte;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\RequestStack;

class EstAccepteController extends AbstractController
{

    #[Route("/notification/estAccepte/{trajetId}/{utilisateurId}", name:"app.estAccepte")]
    public function adopterUnTrajet(EntityManagerInterface $manager, int $trajetId, int $utilisateurId,NotificationService $notificationService,RequestStack $requestStack): Response
    {
        $trajet = $manager->getRepository(Trajets::class)->find($trajetId);

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
        //$this->addFlash('success', "L'utilisateur ".$utilisateur->getNom(). " a adopté le trajet pour : ". $trajet->getArriveA());
        // On redirige l'utilisateur à la page où il était
        $previousUrl = $requestStack->getCurrentRequest()->headers->get('Referer');
        return $this->redirect($previousUrl);
        //return new Response('L\'utilisateur ' . $utilisateur->getNom() . ' a adopté le trajet pour : ' . $trajet->getArriveA());//à revoir la redirection
    }

    //TODO:RefuserPassager
}
