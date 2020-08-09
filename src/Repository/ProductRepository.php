<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
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

    public function getProduct($filter = [])
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.user','u')
            ->andWhere('p.deletedAt is null')
            ->orderBy('p.createdAt','desc');

        if(!empty($filter['user'])){
            $qb->andWhere('p.user = :user')
                ->setParameter('user', $filter['user']);
        }

        if(!empty($filter['wishlist'])){
            $qb->leftJoin('p.wishlists', 'w')
                ->andWhere('w.user = :wishlistUser')
                ->setParameter('wishlistUser', $filter['wishlistUser']);;
        }

        if(!empty($filter['isSold'])){
            $qb->andWhere('p.isSold is null');
        }

        if(!empty($filter['limit'])){
            $qb->setMaxResults($filter['limit']);
        }

        if(!empty($filter['category'])){
            $qb->leftJoin('p.categories', 'c')
                ->andWhere('c.id = :category')
                ->setParameter('category', $filter['category']);;
        }

        if(!empty($filter['search'])){
            $qb->andWhere('p.title LIKE :search or u.email LIKE :search')
                ->setParameter('search', '%'.$filter['search'].'%');;
        }

        if(!empty($filter['sorting'])){
            switch ($filter['sorting']){
                case 'priceAsc':
                    $qb->orderBy('p.price','asc');
                    break;
                case 'priceDesc':
                    $qb->orderBy('p.price','desc');
                    break;
                case 'createdAsc':
                    $qb->orderBy('p.createdAt','asc');
                    break;
                case 'createdDesc':
                    $qb->orderBy('p.createdAt','desc');
                    break;
            }
        }

        if(!empty($filter['withoutResult'])){
            return $qb;
        }

        return $qb
            ->getQuery()
            ->getResult();
    }


}
