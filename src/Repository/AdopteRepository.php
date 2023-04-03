<?php

namespace App\Repository;

use App\Entity\Adopte;
use App\Entity\Utilisateurs;
use App\Entity\Trajets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends ServiceEntityRepository<Adopte>
 *
 * @method Adopte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adopte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adopte[]    findAll()
 * @method Adopte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdopteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adopte::class);
    }

    public function adopterTrajet($trajetId, $utilisateurId)
    {
        $entityManager = $this->getEntityManager();

        $adopte = new Adopte();
        $adopte->setTrajet($trajetId);
        $adopte->setUtilisateur($utilisateurId);

        $entityManager->persist($adopte);
        $entityManager->flush();
    }

    public function abandonnerTrajet($adoptionId)
    {
        $entityManager = $this->getEntityManager();

        $adoption = $entityManager->getRepository(Adopte::class)->find($adoptionId);

        if (!$adoption) {
            throw $this->createNotFoundException('L\'utilisateur n\'est plus en attente d\'insciption.');
        }

        $entityManager->remove($adoption);
        $entityManager->flush();

        return new Response('L\'utilisateur a abandonné le trajet.');
    }

    public function findAllUtilisateur(Trajets $trajet): array
    {
        $adopteRepository = $this->getEntityManager()->getRepository(EstDans::class);
        $adopte = $adopteRepository->findBy(['trajets' => $trajet]);
        
        $utilisateurs = [];
        foreach ($adopte as $adopteItem) {
            $utilisateurs[] = $adopteItem->getUtilisateur();
        }
        
        return $utilisateurs;
    }
}

?>