<?php

namespace App\Controller\admin;

use App\Entity\Category;
use App\Service\ProductService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController {

    /**
     * @Route("/", name="home")
     * @Template("admin/section/demo.html.twig")
     */
    public function indexAction(Request $request, ProductService $productService){
        $filter = [];
        if($request->get('sorting')){
            $filter['sorting'] = $request->get('sorting');
        }
        $paginateProducts = $productService->paginatedProduct($request, $filter);

        return [
            'categories' => $productService->getCategories(),
            'paginateProducts' => $paginateProducts
        ];
    }

    /**
     * @Route("/product/categoryManagement", name="product_category_management")
     * @Template("admin/section/category-management.hmtl.twig")
     */
    public function categoryManagementAction(Request $request){
        $categories = $this->getDoctrine()->getRepository(Category::class)->findBy(['deletedAt'=>null]);

        return [
          'categories' => $categories
        ];
    }

    /**
     * @Route("/product/categoryManagement/add", name="product_category_add")
     */
    public function addCategoryAction(Request $request, ProductService $productService){
        $title = $request->get('title');
        try{
            if(!$title){
                throw new \Exception('Kategori eklenemedi');
            }
            $category = $productService->addNewCategory($title);
            $this->addFlash('success', 'Eklendi');
        }catch (\Exception $exception){
            $this->addFlash('error', $exception->getMessage());
        }

        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    /**
     * @Route("/product/categoryManagement/remove", name="product_category_remove")
     */
    public function removeCategoryAction(Request $request, ProductService $productService){
        $id = $request->get('id');
        try{
            if(!$id){
                throw new \Exception('Kategori silinemedi');
            }
            $productService->removeCategoryById($id);
            $this->addFlash('success', 'Silindi');
        }catch (\Exception $exception){
            $this->addFlash('error', $exception->getMessage());
        }

        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    /**
     * @Route("/product/categoryManagement/subCategory/add", name="product_category_sub_add")
     */
    public function addSubCategoryAction(Request $request, ProductService $productService){
        $title = $request->get('title');
        $categoryId = $request->get('categoryId');
        try{
            if(!$title || !$categoryId){
                return new JsonResponse(['message'=>'Kategori silinemedi']);
            }
            $subCategory = $productService->addNewSubCategory($categoryId, $title);
            $this->addFlash('success', 'Eklendi');
        }catch (\Exception $exception){
            $this->addFlash('success', $exception->getMessage());
        }

        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    /**
     * @Route("/product/categoryManagement/subCategory/remove", name="product_category_sub_remove")
     */
    public function removeSubCategoryAction(Request $request, ProductService $productService){
        $id = $request->get('id');
        try{
            if(!$id){
                return new JsonResponse(['message'=>'Kategori silinemedi']);
            }
            $productService->removeSubCategoryById($id);
        }catch (\Exception $exception){
            $this->addFlash('success', $exception->getMessage());
            $this->addFlash('success', 'Silindi');
        }

        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    /**
     * @Route("/product/categoryManagement/subCategory/get", name="product_category_get_sub")
     */
    public function getSubCategoryAction(Request $request, ProductService $productService){
        $id = $request->get('id');
        try{
            if(!$id){
                return new JsonResponse(['message'=>'Kategori Bulunamadı']);
            }
            $getSubCategories = $this->getDoctrine()->getRepository(Category::class)->getSubcategoriesByCategoryId($id);
            return new JsonResponse(['subCategories'=>$getSubCategories]);
        }catch (\Exception $exception){
            return new JsonResponse(['message'=>$exception->getMessage()], 400);
        }
    }
}