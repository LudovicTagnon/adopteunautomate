<?php

namespace App\Repository;

use App\Entity\Trajets;
use App\Entity\Villes;
use App\Repository\VillesRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trajets::class);
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
    
    public function findByCritere($villeDepart, $villeArrivee, $jourDepart)
    {
        $queryBuilder = $this->createQueryBuilder('t');

        // Si la ville de départ est saisie
        if ($villeDepart) {
            $villeDepartEntity = $this->getEntityManager()->getRepository(Villes::class)->findOneBy(['nom_ville' => $villeDepart]);
            if ($villeDepartEntity) {
                $queryBuilder->andWhere('t.demarrea = :villeDepart')
                    ->setParameter('villeDepart', $villeDepartEntity->getId());
            }
        }

        // Si la ville d'arrivée est saisie
        if ($villeArrivee) {
            $villeArriveeEntity = $this->getEntityManager()->getRepository(Villes::class)->findOneBy(['nom_ville' => $villeArrivee]);
            if ($villeArriveeEntity) {
                $queryBuilder->andWhere('t.arrivea = :villeArrivee')
                    ->setParameter('villeArrivee', $villeArriveeEntity->getId());
            }
        }

        // Si la date est saisie
        if ($jourDepart) {
            $queryBuilder->andWhere('t.T_depart >= :jourDepart')
                ->setParameter('jourDepart', $jourDepart);
        } else {
            // Sinon, la date par défaut est aujourd'hui
            $queryBuilder->andWhere('t.T_depart >= :today')
                ->setParameter('today', new \DateTime('today'));
        }

        // Vérifier que l'une des deux villes a été saisie
        if (!$villeDepart && !$villeArrivee) {
            return [];
        }

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
}
