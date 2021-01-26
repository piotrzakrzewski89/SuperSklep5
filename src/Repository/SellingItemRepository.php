<?php

namespace App\Repository;

use App\Entity\SellingItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SellingItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method SellingItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method SellingItem[]    findAll()
 * @method SellingItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SellingItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SellingItem::class);
    }

    // /**
    //  * @return SellingItem[] Returns an array of SellingItem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SellingItem
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
