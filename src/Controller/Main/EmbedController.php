<?php

namespace App\Controller\Main;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class EmbedController extends AbstractController
{
    public function showSimilarProducts(ProductRepository $productRepository, int $productCount = 2, $resultShuffle = false, int $categoryId = null): Response
    {
        $criteria = ['isDeleted' => false];

        if ($categoryId) {
            $criteria['category'] = $categoryId;
        }

        $products = $productRepository->findBy($criteria, ['id' => 'DESC'], $productCount);

        while ($resultShuffle) {
            $resultShuffle = !shuffle($products);
        }

        return $this->render('main/_embed/_similar_products.html.twig', compact('products'));
    }
}
