<?php

namespace App\Utils\Manager;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Utils\File\ImageResizer;
use App\Utils\Filesystem\FilesystemWorker;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductImageManager extends AbstractBaseManager
{
    /**
     * @var FilesystemWorker
     */
    private $filesystemWorker;

    /**
     * @var ImageResizer
     */
    private $imageResizer;

    /**
     * @var string
     */
    private $uploadsTempDir;

    public function __construct(EntityManagerInterface $em, FilesystemWorker $filesystemWorker, ImageResizer $imageResizer, string $uploadsTempDir)
    {
        parent::__construct($em);
        $this->filesystemWorker = $filesystemWorker;
        $this->imageResizer = $imageResizer;
        $this->uploadsTempDir = $uploadsTempDir;
    }

    public function getProductImagesDir(Product $product): string
    {
        return sprintf('%s/%s', $this->productImagesDir, $product->getId());
    }

    public function getRepository(): ObjectRepository
    {
        return $this->em->getRepository(ProductImage::class);
    }

    public function saveImageForProduct(Product $product, string $tempImageFileName = null): ?ProductImage
    {
        if (!$tempImageFileName) {
            return null;
        }

        $productDir = $this->getProductImagesDir($product);
        $this->filesystemWorker->createFolderIfItNotExists($productDir);

        $filenameId = uniqid();

        $imageSmall = $this->imageResizer->resizeImageAndSave($this->uploadsTempDir, $tempImageFileName, [
            'width' => 60,
            'height' => null,
            'newFolder' => $productDir,
            'newFilename' => sprintf('%s_%s.jpg', $filenameId, 'small'),
        ]);

        $imageMiddle = $this->imageResizer->resizeImageAndSave($this->uploadsTempDir, $tempImageFileName, [
            'width' => 430,
            'height' => null,
            'newFolder' => $productDir,
            'newFilename' => sprintf('%s_%s.jpg', $filenameId, 'middle'),
        ]);

        $imageBig = $this->imageResizer->resizeImageAndSave($this->uploadsTempDir, $tempImageFileName, [
            'width' => 800,
            'height' => null,
            'newFolder' => $productDir,
            'newFilename' => sprintf('%s_%s.jpg', $filenameId, 'big'),
        ]);

        $productImage = new ProductImage();
        $productImage
            ->setFilenameSmall($imageSmall)
            ->setFilenameMiddle($imageMiddle)
            ->setFilenameBig($imageBig)
        ;

        return $productImage;
    }

    public function removeImageFromProduct(ProductImage $productImage)
    {
        $product = $productImage->getProduct();
        $productDir = $this->getProductImagesDir($product);

        $smallFilePath = $productDir.'/'.$productImage->getFilenameSmall();
        $middleFilePath = $productDir.'/'.$productImage->getFilenameMiddle();
        $bigFilePath = $productDir.'/'.$productImage->getFilenameBig();

        $this->filesystemWorker
            ->remove($smallFilePath)
            ->remove($middleFilePath)
            ->remove($bigFilePath);

        $product->removeProductImage($productImage);
        $this->em->flush();
    }
}
