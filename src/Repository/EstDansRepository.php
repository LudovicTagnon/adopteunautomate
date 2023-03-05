<?php

namespace App\Repository;

use App\Entity\EstDans;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EstDans>
 *
 * @method EstDans|null find($id, $lockMode = null, $lockVersion = null)
 * @method EstDans|null findOneBy(array $criteria, array $orderBy = null)
 * @method EstDans[]    findAll()
 * @method EstDans[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstDansRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EstDans::class);
    }

    public function ajouterDans($groupeId, $utilisateurId)
    {
        $entityManager = $this->getEntityManager();

        $estDans = new EstDans();
        $estDans->setGroupes($groupeId);
        $estDans->setUtilisateur($utilisateurId);

        $entityManager->persist($estDans);
        $entityManager->flush();
    }
}

?>