<?php

namespace App\Repository;

use App\Entity\Trajets;
use App\Entity\Villes;
use App\Repository\VillesRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trajets>
 *
 * @method Trajets|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trajets|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trajets[]    findAll()
 * @method Trajets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrajetsRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry,EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Trajets::class);
        $this->entityManager = $entityManager;
    }

    public function save(Trajets $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Trajets $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function findByCritere($user, $villeDepart, $villeArrivee, $jourDepart)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $villeDepartEntity = $this->getEntityManager()->getRepository(Villes::class)->findByVille($villeDepart);
        $villeArriveeEntity = $this->getEntityManager()->getRepository(Villes::class)->findByVille($villeArrivee);

        //dump($villeDepartEntity);
        //dump($villeArriveeEntity);

        $queryBuilder->select('t')
            ->from(Trajets::class, 't')
            ->where('t.publie != :user')
            ->setParameter('user',$user)
            ->andWhere('t.T_depart >= :dateDepart')
            ->setParameter('dateDepart', $jourDepart);

        if (!is_null($villeDepartEntity)) {
            $queryBuilder->andWhere('t.demarrea = :villeDepart');
            $queryBuilder->setParameter('villeDepart', $villeDepartEntity->getId());
        }

        if (!is_null($villeArriveeEntity)) {
            $queryBuilder->andWhere('t.arrivea = :villeArrivee');
            $queryBuilder->setParameter('villeArrivee', $villeArriveeEntity->getId());
        }

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    public function findByCritereDate($user,$jourDepart)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        //dump($villeDepartEntity);
        //dump($villeArriveeEntity);

        $queryBuilder->select('t')
            ->from(Trajets::class, 't')
            ->where('t.publie != :user')
            ->setParameter('user',$user)
            ->andWhere('t.T_depart >= :dateDepart')
            ->setParameter('dateDepart', $jourDepart);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
}
