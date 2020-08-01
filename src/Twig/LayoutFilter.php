<?php

namespace App\Twig;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Wishlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class LayoutFilter extends AbstractExtension
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var User */
    private $user;

    /**
     * LayoutFilter constructor.
     */
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->user = $security->getUser();
    }

    public function getFilters()
    {
        return [
            new TwigFilter('isInWhishlist', [$this, 'isInWhishlist']),
        ];
    }

    public function isInWhishlist(Product $product)
    {
        $whishlist = $this->em->getRepository(Wishlist::class)->findOneBy(['product'=>$product, 'user'=>$this->user]);
        return $whishlist;
    }
}