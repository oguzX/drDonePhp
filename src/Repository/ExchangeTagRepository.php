<?php

namespace App\Repository;

use App\Entity\ExchangeTag;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExchangeTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExchangeTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExchangeTag[]    findAll()
 * @method ExchangeTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExchangeTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeTag::class);
    }

    public function getExchangeableProducts(Product $product)
    {
        return $this->createQueryBuilder('e')
            ->addSelect('p')
            ->leftJoin('e.product','p')
            ->andWhere('e.title IN (:tags)')
            ->andWhere('p <> :product')
            ->setParameter('product', $product)
            ->setParameter('tags', $product->getExchangeTagsTitle())
            ->getQuery()->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?ExchangeTag
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
