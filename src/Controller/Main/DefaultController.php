<?php

namespace App\Controller\Main;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{

    public function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @Route("/", methods="GET", name="main_homepage")
     */
    public function index(): Response
    {
        $productList = $this->getEm()->getRepository(Product::class)->findAll();
        // dd($productList);
        return $this->render('main/default/index.html.twig', []);
    }

    // /**
    //  * for test
    //  * @Route("/product-add", methods="GET", name="product_add_old")
    //  */
    // public function productAdd(): Response
    // {
    //     $product = new Product();
    //     $product->setTitle('Product. ' . rand(1, 100))
    //         ->setDescription('something')
    //         ->setPrice(rand(10, 100))
    //         ->setQuantity(1);

    //     $this->getEm()->persist($product);
    //     $this->getEm()->flush();

    //     // return $this->render('main/default/index.html.twig', $product);
    //     return $this->redirectToRoute('homepage');
    // }

    // /**
    //  * @Route("/edit-product/{id}", methods="GET|POST", name="product_edit", requirements={"id"="\d+"})
    //  * @Route("/add-product", methods="GET|POST", name="product_add")
    //  */
    // public function editProduct(Request $request, int $id = null): Response
    // {
    //     if ($id) {
    //        $product = $this->getEm()->getRepository(Product::class)->find($id);
    //     } else {
    //         // throw new \LogicException('ID needed');
    //         $product = new Product();
    //     }

    //     $form = $this->createForm(EditProductFormType::class, $product);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $this->getEm()->persist($product);
    //         $this->getEm()->flush();

    //         return $this->redirectToRoute('product_edit', [ 'id' => $product->getId() ]);
    //     }
    
    //     // dd($product, $form);
    //     return $this->render('main/default/edit_product.html.twig', [
    //         'form' => $form->createView()
    //     ]);
    // }
}
