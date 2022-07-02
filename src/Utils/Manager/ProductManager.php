<?php

namespace App\Utils\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use App\Utils\Manager\AbstractBaseManager;
use App\Entity\Product;



class ProductManager extends AbstractBaseManager
{

    /**
     *
     * @var ProductImageManager
     */
    private $productImageManager;

    /**
     *
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

    /**
     *
     * @param Product $product
     * @return string
     */
    public function getProductImagesDir(Product $product): string
    {
        return sprintf('%s/%s', $this->productImagesDir, $product->getId());
    }

    /**
     *
     * @param Product $product
     * @param string|null $tempImageFilename
     * @return Product
     */
    public function updateProductImages(Product $product, string $tempImageFilename = null): Product
    {
        if (!$tempImageFilename) {
            return $product;
        }

        $productDir = $this->getProductImagesDir($product);

        $productImage = $this->productImageManager->saveImageForProduct($productDir, $tempImageFilename);
        $productImage->setProduct($product);
        $product->addProductImage($productImage);

        return $product;
    }
}