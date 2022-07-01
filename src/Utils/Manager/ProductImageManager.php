<?php

namespace App\Utils\Manager;

use App\Entity\ProductImage;
use App\Utils\File\ImageResizer;
use App\Utils\Filesystem\FilesystemWorker;
use Doctrine\ORM\EntityManagerInterface;

class ProductImageManager
{

    /**
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     *
     * @var FilesystemWorker
     */
    private $filesystemWorker;

    /**
     *
     * @var ImageResizer
     */
    private $imageResizer;

    /**
     *
     * @var string
     */
    private $uploadsTempDir;


    public function __construct(EntityManagerInterface $em, FilesystemWorker $filesystemWorker, ImageResizer $imageResizer, string $uploadsTempDir)
    {
        $this->em = $em;
        $this->filesystemWorker = $filesystemWorker;
        $this->imageResizer = $imageResizer;
        $this->uploadsTempDir = $uploadsTempDir;
    }

    /**
     *
     * @param string $productDir
     * @param string|null $tempImageFileName
     * @return ProductImage|null
     */
    public function saveImageForProduct(string $productDir, string $tempImageFileName = null): ?ProductImage
    {
        if (!$tempImageFileName) {
            return null;
        }

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
        ]);;

        $imageBig = $this->imageResizer->resizeImageAndSave($this->uploadsTempDir, $tempImageFileName, [
            'width' => 800,
            'height' => null,
            'newFolder' => $productDir,
            'newFilename' => sprintf('%s_%s.jpg', $filenameId, 'big'),
        ]);;

        $productImage = new ProductImage();
        $productImage
            ->setFilenameSmall($imageSmall)
            ->setFilenameMiddle($imageMiddle)
            ->setFilenameBig($imageBig)
        ;

        return $productImage;
    }

    public function removeImageFromProduct(ProductImage $productImage, string $productDir)
    {
        $smallFilePath = $productDir . '/' . $productImage->getFilenameSmall();
        $middleFilePath = $productDir . '/' . $productImage->getFilenameMiddle();
        $bigFilePath = $productDir . '/' . $productImage->getFilenameBig();

        $this->filesystemWorker
            ->remove($smallFilePath)
            ->remove($middleFilePath)
            ->remove($bigFilePath);

        $productImage->getProduct()->removeProductImage($productImage);
        $this->em->flush();
    }
}
