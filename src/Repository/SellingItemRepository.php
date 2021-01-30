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

    /**
     * @return SellingItem[] Returns an array of SellingItem objects
     */
    public function findAllItemsId($value)
    {

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->add('select', 's')
            ->add('from', '\App\Entity\SellingItem s')
            ->add('where', $qb->expr()->in('s.id', $value));

        $q = $qb->getQuery()->execute();

        return $q;
    }
}
