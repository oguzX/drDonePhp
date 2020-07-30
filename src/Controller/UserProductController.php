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
 * @Route("/user/product")
 *
 */
class UserProductController extends AbstractController {

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

    /**
     * @Route("/add", name="user_product_add")
     * @Template("front/sections/product/form/product.html.twig")
     */
    public function addAction(Request $request, ProductService $productService){
        $form = $this->createForm(ProductType::class);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{
                $productService->createProductByForm($form);
                $this->addFlash('success', 'Ürün Eklendi!');
            }catch (\Exception $exception){
                $this->addFlash('error', 'Sistem Hatası: '. $exception->getMessage());
            }
        }

        return [
            'productForm' => $form->createView()
        ];
    }


    /**
     * @Route("/{id}/sold", name="user_product_set_sold")
     *
     */
    public function setSoldAction(Request $request, Product $product){
        try{
            $product->setIsSold(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success','Satıldı Olarak Ayarlandı!');
        }catch (\Exception $exception){
            $this->addFlash('error','Satıldı Olarak Ayarlanamadı!');
        }

        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }
}