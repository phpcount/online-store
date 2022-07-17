<?php

namespace App\Form\Handler;

use App\Entity\Product;
use App\Form\DTO\EditProductModel;
use App\Utils\File\FileSaver;
use App\Utils\Manager\ProductManager;
use Symfony\Component\Form\Form;

class ProductFormHandler
{
    /**
     * @var ProductManager
     */
    private $productManager;

    /**
     * @var FileSaver
     */
    private $fileSaver;

    public function __construct(ProductManager $productManager, FileSaver $fileSaver)
    {
        $this->productManager = $productManager;
        $this->fileSaver = $fileSaver;
    }

    public function processEditForm(EditProductModel $editProductModel, Form $form): Product
    {
        // TODO: ADD A NEW IMAGE WITH DIFFERENT SIZES TO THE PRODUCT
        // 1. Save product's changes (+)

        // 2. Save uploaded file into temp folder (+)

        // 3. Work with Product (addProductImage) and ProductImage
        // 3.1 Get path of folder with images of product (+)

        // 3.2 Work with ProductImage
        // 3.2.1 Resize and save image into folder (BIG, MIDDLE, SMALL) (+)
        // 3.2.2 Create ProdcutImage it to for Prodcut (+)

        // 3.3 Save Product with new ProductImage (+)

        $product = new Product();
        if ($editProductModel->id) {
            $product = $this->productManager->find($editProductModel->id);
        }

        $product = $editProductModel->makeProductFromModel($product);

        $newImageFile = $form->get('newImage')->getData();

        $tempImageFileName = $newImageFile
            ? $this->fileSaver->saveUploadedFileIntoTemp($newImageFile)
            : null;

        if ($product->getTitle() && null === $product->getId()) {
            $this->productManager->save($product); // fix
        }

        $product = $this->productManager->updateProductImages($product, $tempImageFileName);

        $this->productManager->save($product);

        return $product;
    }
}
