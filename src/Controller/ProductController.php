<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Wishlist;
use App\Service\ProductService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function detailAction(Product $product, ProductService $productService){
        $otherProducts = $this->getDoctrine()->getRepository(Product::class)->findBy(['user'=>$this->getUser(),'isSold'=>0],['createdAt'=>'desc'],4);

        return [
            'product' => $product,
            'otherProducts' => $otherProducts,
            'categories' => $productService->getCategories()
        ];
    }

    /**
     * @Route("/{handle}/addToWishlist", name="product_whislist_add")
     */
    public function wishlistAdd(Product $product, ProductService $productService){
        try{
            $productService->addToWishlist($product);
            return new JsonResponse(['error'=>0,'message'=>$product->getTitle().' istek listene eklendi']);
        }catch (\Exception $exception){
            return new JsonResponse(['error'=>1,'message'=>'Sistemsel bir hata meydana geldi, '.$exception->getMessage()], 500);
        }
    }

    /**
     * @Route("/{id}/removeToWishlist", name="product_whislist_remove")
     */
    public function wishlistRemove(Wishlist $wishlist, ProductService $productService){
        try{
            $productService->removeToWishlist($wishlist);
            return new JsonResponse(['error'=>0,'message'=>'Ürün istek listenden çıkarıldı']);
        }catch (\Exception $exception){
            return new JsonResponse(['error'=>1,'message'=>'Sistemsel bir hata meydana geldi, '.$exception->getMessage()], 500);
        }
    }

    /**
     * @Route("/{handle}/toggleWishlist", name="product_whislist_toggle")
     */
    public function wishlistToggle(Product $product, ProductService $productService){
        try{
            $response = $productService->toggleWishlist($product);
            return new JsonResponse(['error'=>0,'message'=>$response]);
        }catch (\Exception $exception){
            return new JsonResponse(['error'=>1,'message'=>'Sistemsel bir hata meydana geldi, '.$exception->getMessage()], 500);
        }
    }

    /**
     * @Route("/wishlist", name="product_whislist")
     */
    public function wishlistList(Product $product, ProductService $productService){
        $products = $this->getDoctrine()->getRepository(Product::class)->getProduct(['user' => $this->getUser()->getId()]);
        return [
            'products' => $products,
            'layoutType' => 'product-wishlist'
        ];
    }
}