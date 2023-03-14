<?php

namespace App\Repository;

use App\Entity\Villes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Villes>
 *
 * @method Villes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Villes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Villes[]    findAll()
 * @method Villes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VillesRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Villes::class);
        $this->entityManager = $entityManager;
    }

    public function save(Villes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Villes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllCity(): array
    {
        //$villeRepository = $this->getEntityManager()->getRepository(Ville::class);
        $savedCity = $this->findAll();

        $cities = [];
        foreach($savedCity as $cityItem){
            $cities[] = $cityItem->getNomVille();
        }
        return $cities;
    }

    public function findByVille(string $ville): ?Villes
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $replacedName = str_replace(' ', '', $ville);

        $queryBuilder->select('v')
            ->from(Villes::class,'v')
            ->andWhere('upper(v.nom_ville) LIKE :term')
            ->setParameter('term', '%'.strtoupper($replacedName).'%')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        $results = $queryBuilder->getQuery()->getResult();
        dump($results);
        return isset($results[0]) ? $results[0] : null;
    }

}

