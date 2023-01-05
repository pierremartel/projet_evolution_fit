<?php

namespace App\Repository;

use App\Entity\PurchaseShipping;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PurchaseShipping>
 *
 * @method PurchaseShipping|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseShipping|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseShipping[]    findAll()
 * @method PurchaseShipping[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseShippingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseShipping::class);
    }

    public function add(PurchaseShipping $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PurchaseShipping $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PurchaseShipping[] Returns an array of PurchaseShipping objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PurchaseShipping
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
