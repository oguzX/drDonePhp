<?php


namespace App\Service;


use App\Entity\Images;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Easybook\Slugger;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProductService
{

    /** @var EntityManagerInterface */
    private $em;

    /** @var Slugger */
    private $slug;

    /**
     * ProductService constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->slug = new Slugger();
    }

    public function addImagesToProduct(Product $product, $imagesFile, $flush = false){
        if ($imagesFile) {
            foreach ($imagesFile as $imageFile){
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $this->slug->slugify($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_path'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $newImage = new Images();
                $newImage->setUrl($newFilename);
                $product->addImage($newImage);
            }
        }else{
            return false;
        }
        if($flush){
            $this->em->persist($product);
            $this->em->flush();
        }
        return $product;
    }
}