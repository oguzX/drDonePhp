<?php

namespace App\Twig;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Wishlist;
use App\Service\ProductService;
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

    /** @var ProductService */
    private $productService;

    /**
     * LayoutFilter constructor.
     */
    public function __construct(EntityManagerInterface $em, Security $security, ProductService $productService)
    {
        $this->em = $em;
        $this->user = $security->getUser();
        $this->productService = $productService;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('isInWhishlist', [$this, 'isInWhishlist']),
            new TwigFilter('exchangeableProduct', [$this, 'exchangeableProducts']),
        ];
    }

    public function isInWhishlist(Product $product)
    {
        $whishlist = $this->em->getRepository(Wishlist::class)->findOneBy(['product'=>$product, 'user'=>$this->user]);
        return $whishlist;
    }

    public function exchangeableProducts(Product $product)
    {
        $products = $this->productService->getExchangeableProducts($product);

        return $products;
    }
}