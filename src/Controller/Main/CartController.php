<?php

namespace App\Controller\Main;

use App\Utils\Manager\CartManager;
use App\Utils\Manager\OrderManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cart", name="main_cart_")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="show")
     */
    public function show(Request $request, CartManager $cartManager): Response
    {
        $phpSessionId = $request->cookies->get('PHPSESSID');
        $cart = $cartManager->getRepository()->findOneBy(['sessionId' => $phpSessionId]);

        return $this->render('main/cart/show.html.twig', compact('cart'));
    }
    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, OrderManager $orderManager): Response
    {
        $phpSessionId = $request->cookies->get('PHPSESSID');
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('main_login');
        }

        $orderManager->createOrderFromCartBySessionId($phpSessionId, $user);
        // dd($phpSessionId);

        return $this->redirectToRoute('main_cart_show');
    }
}
