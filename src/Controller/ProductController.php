<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product", name="main_product_")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/{uuid}", name="show")
     */
    public function show(Product $product = null): Response
    {
        if (!$product) {
            throw new NotFoundHttpException();
        }

        return $this->render('main/product/show.html.twig', compact('product'));
    }


}
