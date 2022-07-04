<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\DTO\EditProductModel;
use App\Form\Admin\EditProductFormType;
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

        $editProductModel = EditProductModel::makeFromProduct($product);
        
        $form = $this->createForm(EditProductFormType::class, $editProductModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $productFormHandler->processEditForm($editProductModel, $form);
            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('admin_product_edit', ['id' => $product->getId()]);
        } else {
            // $this->addFlash('danger', 'Not valid.');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('warning', 'Something went wrong. Please check your form');
        }

        $images = $product->getProductImages()
            ? $product->getProductImages()->getValues()
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
        $title = $product->getTitle();
        $productManager->remove($product, true);

        $this->addFlash('info', sprintf('The product: "%s" has been successfully deleted.', $title));

        return $this->redirectToRoute('admin_product_list');
    }
}
