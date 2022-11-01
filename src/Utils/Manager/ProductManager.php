<?php

namespace App\Utils\Manager;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductManager extends AbstractBaseManager
{
    /**
     * @var ProductImageManager
     */
    private $productImageManager;

    /**
     * @var string
     */
    private $productImagesDir;

    public function __construct(EntityManagerInterface $em, ProductImageManager $productImageManager, string $productImagesDir)
    {
        parent::__construct($em);
        $this->productImageManager = $productImageManager;
        $this->productImagesDir = $productImagesDir;
    }

    public function getRepository(): ObjectRepository
    {
        return $this->em->getRepository(Product::class);
    }


    public function updateProductImages(Product $product, string $tempImageFilename = null): Product
    {
        if (!$tempImageFilename) {
            return $product;
        }

        $productImage = $this->productImageManager->saveImageForProduct($product, $tempImageFilename);
        $productImage->setProduct($product);
        $product->addProductImage($productImage);

        return $product;
    }
}
