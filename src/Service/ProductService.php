<?php


namespace App\Service;


use App\Entity\Category;
use App\Entity\Images;
use App\Entity\Product;
use App\Entity\Wishlist;
use Doctrine\ORM\EntityManagerInterface;
use Easybook\Slugger;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class ProductService
{

    /** @var EntityManagerInterface */
    private $em;

    /** @var Slugger */
    private $slug;

    /** @var ParameterBagInterface */
    private $parameterBag;

    /** @var UserInterface */
    private $user;

    /**
     * ProductService constructor.
     */
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $parameterBag, Security $security)
    {
        $this->em = $em;
        $this->slug = new Slugger();
        $this->parameterBag = $parameterBag;
        $this->user = $security->getUser();
    }

    public function createProductByForm($productForm){
        /** @var Product $task */
        $product = $productForm->getData();

        $product->setUser($this->user);

        $product = $this->addImagesToProduct($product, [$productForm->get('images')->getData()]);
        $product = $this->setUniqueSlug($product);

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }

    public function addImagesToProduct(Product $product, $imagesFile, $flush = false){
        if ($imagesFile) {
            foreach ($imagesFile as $imageFile){
                $newImage = new Images();
                $newFilename = $this->imageUpload($imageFile);
                $newImage->setUrl($newFilename);
                $product->addImage($newImage);
            }
        }
        $this->flush($product, $flush);
        return $product;
    }

    public function imageUpload($imageFile, $pathParam = 'images_path'){
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slug->slugify($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

        // Move the file to the directory where brochures are stored
        try {
            $imageFile->move(
                $this->parameterBag->get($pathParam),
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $newFilename;
    }

    public function setUniqueSlug(Product $product, $flush = false){
        $productRepo = $this->em->getRepository(Product::class);
        do{
            $slug = $product->getTitle().' '.rand(0,99999);
            $product->setHandle($this->slug->slugify($slug));
        }while($productRepo->findOneBy(['handle'=>$product->getHandle()]));

        $this->flush($product, $flush);

        return $product;
    }

    private function flush($obj, $flush){
        if($flush){
            $this->em->persist($obj);
            $this->em->flush();
        }

        return $obj;
    }

    public function getCategories(){
        return $this->em->getRepository(Category::class)->findAll();
    }

    public function addToWishlist(Product $product){
        $wishlistObj = new Wishlist();
        $wishlistObj->setUser($this->user);
        $wishlistObj->setProduct($product);
        $wishlistObj->setCreatedAt(new \DateTime());

        $this->flush($wishlistObj, true);
    }

    public function toggleWishlist(Product $product){
        $wishlist = $this->em->getRepository(Wishlist::class)->findOneBy(['product'=>$product, 'user'=>$this->user]);
        if($wishlist){
            $this->em->remove($wishlist);
            $this->em->flush();
            $response = 'Ürün istek listenden çıkarıldı';
        }else{
            $wishlistObj = new Wishlist();
            $wishlistObj->setUser($this->user);
            $wishlistObj->setProduct($product);
            $wishlistObj->setCreatedAt(new \DateTime());
            $this->flush($wishlistObj, true);

            $response = $product->getTitle().' istek listene eklendi';
        }

        return $response;
    }

    public function removeToWishlist(Wishlist $wishlist){
        $this->em->remove($wishlist);
        $this->em->flush($wishlist);
    }
}