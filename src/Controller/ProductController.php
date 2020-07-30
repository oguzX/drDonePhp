<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductService;
use App\Type\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 *
 */
class ProductController extends AbstractController {

    /**
     * @Route("/", name="user_product")
     * @Template("front/sections/product/product-management.html.twig")
     */
    public function indexAction(){
        $products = $this->getDoctrine()->getRepository(Product::class)->findBy(['user' => $this->getUser()->getId()]);
        return [
            'products' => $products
        ];
    }
}