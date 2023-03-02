<?php

namespace App\Repository;

use App\Entity\Groupes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Groupes>
 *
 * @method Groupes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Groupes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Groupes[]    findAll()
 * @method Groupes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Groupes::class);
    }

    public function save(Groupes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Groupes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
