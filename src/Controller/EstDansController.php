<?php

namespace App\Controller;

use App\Entity\EstDans;
use App\Entity\Groupes;
use App\Entity\Utilisateurs;
use App\Repository\EstDansRepository;
use App\Repository\GroupesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EstDansController extends AbstractController
{
    #[Route("/groupes/edition/ajouter/{groupeId}/{utilisateurId}", name:"app_groupes.ajouter_utilisateur")]
    public function ajouterUtilisateurDansGroupe(EntityManagerInterface $manager, int $groupeId, int $utilisateurId): Response
    {
        $groupe = $manager->getRepository(Groupes::class)->find($groupeId);
        $utilisateur = $manager->getRepository(Utilisateurs::class)->find($utilisateurId);

        if (!$groupe || !$utilisateur) {
            throw $this->createNotFoundException('Groupe ou utilisateur introuvable.');
        }

        $estDans = new EstDans();
        $estDans->setGroupes($groupe);
        $estDans->setUtilisateur($utilisateur);

        $manager->persist($estDans);
        $manager->flush();

        return new Response('L\'utilisateur ' . $utilisateur->getNom() . ' a été ajouté au groupe ' . $groupe->getNom());
    }

    #[Route("/groupes/edition/supprimer/{groupeId}/{utilisateurId}", name:"app_groupes.supprimer_utilisateur")]
    public function supprimerUtilisateurDuGroupe(EntityManagerInterface $manager, int $groupeId, int $utilisateurId): Response
    {
        $estDansRepository = $manager->getRepository(EstDans::class);
        $estDans = $estDansRepository->findOneBy(['groupes' => $groupeId, 'utilisateurs' => $utilisateurId]);

        if (!$estDans) {
            throw $this->createNotFoundException('L\'utilisateur n\'est pas dans le groupe.');
        }

        $manager->remove($estDans);
        $manager->flush();

        return new Response('L\'utilisateur a été retiré du groupe.');
    }
}

?>