<?php

namespace App\Utils\Manager;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductManager
{

    /**
     *
     * @var EntityManagerInterface
     */
    private $em;

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
        $this->em = $em;
        $this->productImageManager = $productImageManager;
        $this->productImagesDir = $productImagesDir;
    }


    /**
     *
     * @return ObjectRepository
     */
    public function getRepository(): ObjectRepository
    {
        return $this->em->getRepository(Product::class);
    }


    public function save(Product $product)
    {
        $this->em->persist($product);
        $this->em->flush();
    }

    public function remove(Product $product)
    {
        $product->setIsDeleted(true);
        $this->save($product);
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