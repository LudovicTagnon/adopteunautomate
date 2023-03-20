<?php

namespace App\Repository;

use App\Entity\EstAccepte;
use App\Entity\Utilisateurs;
use App\Entity\Trajets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EstAccepte>
 *
 * @method EstAccepte|null find($id, $lockMode = null, $lockVersion = null)
 * @method EstAccepte|null findOneBy(array $criteria, array $orderBy = null)
 * @method EstAccepte[]    findAll()
 * @method EstAccepte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstAccepteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EstAccepte::class);
    }

    public function estAccepte($trajetId, $utilisateurId)
    {
        $entityManager = $this->getEntityManager();

        $estAccepte = new EstAccepte();
        $estAccepte->setTrajet($trajetId);
        $estAccepte->setUtilisateur($utilisateurId);

        $entityManager->persist($estAccepte);
        $entityManager->flush();
    }

    public function save(EstAccepte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EstAccepte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return EstAccepte[] Returns an array of EstAccepte objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EstAccepte
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
