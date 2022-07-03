<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category", name="main_category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/{slug}", name="show")
     */
    public function show(Category $category = null): Response
    {
        if (!$category) {
            throw new NotFoundHttpException("Category not found");
        }

        $products = $category->getProducts()->getValues();

        return $this->render('main/category/show.html.twig', compact('category', 'products'));
    }
}
