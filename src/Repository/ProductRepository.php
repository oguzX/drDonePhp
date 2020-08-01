<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

     /**
      * @return Product[] Returns an array of Product objects
      */
    public function getProduct($filter = [])
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.deletedAt is null')
            ->orderBy('p.createdAt','desc');

        if(!empty($filter['user'])){
            $qb->andWhere('p.user = :user')
                ->setParameter('user', $filter['user']);
        }

        if(!empty($filter['wishlist'])){
            $qb->leftJoin('p.wishlists', 'w')
            ->andWhere('w.user = :user');
        }

        if(!empty($filter['isSold'])){
            $qb->andWhere('p.isSold is null');
        }

        return $qb
            ->getQuery()
            ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
