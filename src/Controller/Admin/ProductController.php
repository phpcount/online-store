<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\EditProductFormType;
use App\Form\Handler\ProductFormHandler;
use App\Repository\ProductRepository;
use App\Utils\Manager\ProductManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/product", name="admin_product_")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/list", methods="GET", name="list")
     */
    public function list(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['isDeleted' => false], ['id' => 'DESC'], 50);
        return $this->render('admin/product/list.html.twig', compact('products'));
    }

    /**
     * @Route("/edit/{id}", methods="GET|POST", name="edit")
     * @Route("/add", methods="GET|POST", name="add")
     */
    public function edit(Request $request, ProductFormHandler $productFormHandler, Product $product = null): Response
    {
        if (!$product) {
            $product = new Product();
        }

        $form = $this->createForm(EditProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $productFormHandler->processEditForm($product, $form);
            return $this->redirectToRoute('admin_product_edit', ['id' => $product->getId()]);
        }

        $images = $product->getProductImages()->getValues()
            ? $product->getProductImages()
            : [];

        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'images' => $images,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", methods="GET|DELETE", name="delete")
     */
    public function delete(Product $product, ProductManager $productManager): Response
    {
        $productManager->remove($product);

        return $this->redirectToRoute('admin_product_list');
    }
}
