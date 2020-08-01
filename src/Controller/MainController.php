<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController {

    /**
     * @Route("/", name="home")
     * @Template("front/sections/product/product-home.html.twig")
     */
    public function indexAction(Request $request, ProductService $productService){
        $paginateProducts = $productService->paginatedProduct($request);

        return [
            'categories' => $productService->getCategories(),
            'paginateProducts' => $paginateProducts
        ];
    }
}