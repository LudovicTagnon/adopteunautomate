<?php

namespace App\Controller;

use App\Entity\Adopte;
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
        $notificationService->addNotificationAdopteTrajet("Vous avez adopté un trajet !", $utilisateur); //notification
        $this->addFlash('success', "L'utilisateur ".$utilisateur->getNom(). " a adopté le trajet pour : ". $trajet->getArriveA());
        // On redirige l'utilisateur à la page où il était
        $previousUrl = $requestStack->getCurrentRequest()->headers->get('Referer');
        return $this->redirect($previousUrl);
        //return new Response('L\'utilisateur ' . $utilisateur->getNom() . ' a adopté le trajet pour : ' . $trajet->getArriveA());//à revoir la redirection
    }

    #[Route("/trajet/abandonner/{trajetId}/{utilisateurId}", name:"app_trajet.abandonner_trajet")]
    public function abandonnerTrajet(AdopteRepository $adopteRepository, EntityManagerInterface $manager, int $trajetId, int $utilisateurId,RequestStack $requestStack,NotificationService $notificationService): Response
    {
        dump($trajetId);
        $adopte = $adopteRepository->findOneBy(['trajet' => $trajetId, 'utilisateur' => $utilisateurId]);
        $utilisateur = $this->getUser();

        dump($adopte);

        if (!$adopte) {
            throw $this->createNotFoundException('L\'utilisateur n\'est pas en attente d\'insciption.');
        }
        
        try {
            $manager->remove($adopte);
            $manager->flush();
        } catch (\Exception $e) {
            throw new \Exception('Une erreur est survenue lors de la suppression de l\'adoption : '.$e->getMessage());
        }
        $notificationService->addNotificationAbandonneTrajet("Vous avez abandonné un trajet !", $utilisateur); //notification
        $this->addFlash('success', "L'utilisateur a abandonné le trajet.");
        // On redirige l'utilisateur à la page où il était
        $previousUrl = $requestStack->getCurrentRequest()->headers->get('Referer');
        return $this->redirect($previousUrl);
    }
}
?>