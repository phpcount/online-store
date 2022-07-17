<?php

namespace App\Controller\Main;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Repository\CartProductRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="main_api_")
 */
class CartApiController extends AbstractController
{
    /**
     * @Route("/cart", methods="POST", name="cart_save")
     */
    public function saveCart(Request $request, CartRepository $cartRepository, CartProductRepository $cartProductRepository, ProductRepository $productRepository): JsonResponse
    {
        $requestArray = $request->toArray();
        extract($requestArray);

        if (empty($productId)) {
            return new JsonResponse(['message' => 'Empty field: productId'], 400);
        }

        $phpSessionId = $request->cookies->get('PHPSESSID');
        if (empty($phpSessionId)) {
            return new JsonResponse(['message' => 'Cookie not found'], 200);
        }

        $product = $productRepository->findOneBy(['uuid' => $productId]);

        $cart = $cartRepository->findOneBy(['sessionId' => $phpSessionId]);
        if (!$cart) {
            $cart = new Cart();
            $cart->setSessionId($phpSessionId);
        }

        $cartProduct = $cartProductRepository->findOneBy(['cart' => $cart, 'product' => $product]);
        if (!$cartProduct) {
            $cartProduct = new CartProduct();
            $cartProduct
                ->setProduct($product)
                ->setCart($cart)
                ->setQuantity(1)
            ;

            $cart->addCartProduct($cartProduct);
        } else {
            $newQuantity = $cartProduct->getQuantity() + 1;
            $cartProduct->setQuantity($newQuantity);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->persist($cartProduct);
        $em->flush();

        return new JsonResponse([
            'success' => false,
            'data' => [
                'num' => 150,
                'test' => 'qwerty',
                'request' => $product,
            ],
        ]);
    }
}
