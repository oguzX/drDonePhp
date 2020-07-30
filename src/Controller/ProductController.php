<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 *
 */
class ProductController extends AbstractController {

    /**
     * @Route("/{handle}", name="product_detail")
     * @Template("front/sections/product/product-detail.html.twig")
     */
    public function detailAction(Product $product){
        $otherProducts = $this->getDoctrine()->getRepository(Product::class)->findBy(['user'=>$this->getUser(),'isSold'=>0],['createdAt'=>'desc'],4);
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return [
            'product' => $product,
            'otherProducts' => $otherProducts,
            'categories' => $categories,
        ];
    }
}