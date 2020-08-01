<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Product;
use App\Service\ProductService;
use App\Type\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Template("front/sections/product/product-list.html.twig")
     */
    public function indexAction(){
        $products = $this->getDoctrine()->getRepository(Product::class)->getProduct(['user' => $this->getUser()->getId()]);
        return [
            'products' => $products,
            'layoutType' => 'product-management'
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
     * @Route("/{id}/edit", name="user_product_edit")
     * @Template("front/sections/product/form/product.html.twig")
     */
    public function editAction(Request $request, Product $product, ProductService $productService){
        $form = $this->createForm(ProductType::class, $product);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{
                $productService->updateProduct($form->getData());
                $this->addFlash('success', 'Ürün Düzenlendi!');
            }catch (\Exception $exception){
                $this->addFlash('error', 'Sistem Hatası: '. $exception->getMessage());
            }
        }

        return [
            'product' => $product,
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

    /**
     * @Route("/{id}/delete", name="user_product_set_delete")
     *
     */
    public function setDeleteAction(Request $request, Product $product){
        try{
            $product->setDeletedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success','Silindi!');
        }catch (\Exception $exception){
            $this->addFlash('error','Silinemedi!');
        }

        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    /**
     * @Route("/{id}/image/update", name="user_product_image_update")
     *
     */
    public function imageUpdateAction(Request $request, Product $product, ProductService $productService){
        try{
            $productService->addImagesToProduct($product, $request->files->all(), true);
            return new JsonResponse(['error' => 0, 'message' => ' Fotograf Yuklendi']);
        }catch (\Exception $exception){
            return new JsonResponse(['error' => 1, 'message' => 'Sistemsel Hata: ' . $exception->getMessage()]);
        }
    }

    /**
     * @Route("/image/{id}/remove", name="user_product_image_remove")
     *
     */
    public function imageRemoveAction(Request $request, Images $image, ProductService $productService)
    {
        try {
            $productService->removeObject($image, true);
            return new JsonResponse(['error' => 0, 'message' => ' Fotograf silindi']);
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => 1, 'message' => 'Sistemsel Hata: ' . $exception->getMessage()]);
        }
    }
}