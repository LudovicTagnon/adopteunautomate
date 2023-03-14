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

class AdopteController extends AbstractController
{
    #[Route("/trajet/adopter/{trajetId}/{utilisateurId}", name:"app_trajet.adopter_trajet")]
    public function adopterUnTrajet(EntityManagerInterface $manager, int $trajetId, int $utilisateurId): Response
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

        return new Response('L\'utilisateur ' . $utilisateur->getNom() . ' a adopté le trajet pour : ' . $trajet->getArriveA());
    }

    #[Route("/trajet/abandonner/{trajetId}/{utilisateurId}", name:"app_trajet.abandonner_trajet")]
    public function abandonnerTrajet(AdopteRepository $adopteRepository, EntityManagerInterface $manager, int $trajetId, int $utilisateurId): Response
    {
        dump($trajetId);
        $adopte = $adopteRepository->findOneBy(['trajet' => $trajetId, 'utilisateur' => $utilisateurId]);

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

        return new Response('L\'utilisateur a abandonné le trajet.');
    }
}
?>