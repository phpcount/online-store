<?php

namespace App\Controller\Admin;

use App\Entity\ProductImage;
use App\Utils\Manager\ProductImageManager;
use App\Utils\Manager\ProductManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/product-image", name="admin_product_image_")
 */
class ProductImageController extends AbstractController
{
    /**
     * @Route("/delete/{id}", methods="GET|DELETE", name="delete")
     */
    public function delete(ProductImage $productImage, ProductImageManager $productImageManager): Response
    {
        if (!$productImage) {
            return $this->redirectToRoute('admin_product_list');
        }

        $productImageManager->removeImageFromProduct($productImage);
        $product = $productImage->getProduct();

        return $this->redirectToRoute('admin_product_edit', ['id' => $product->getId()]);
    }
}
