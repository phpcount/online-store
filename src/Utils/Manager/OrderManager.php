<?php

namespace App\Utils\Manager;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\StaticStorage\OrderStaticStorage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderManager extends AbstractBaseManager
{
    /**
     * @var CartManager
     */
    private $cartManager;

    public function __construct(EntityManagerInterface $em, CartManager $cartManager)
    {
        parent::__construct($em);

        $this->cartManager = $cartManager;
    }

    public function getRepository(): ObjectRepository
    {
        return $this->em->getRepository(Order::class);
    }

    /**
     * @return void
     */
    public function createOrderFromCartBySessionId(string $sessionId, UserInterface $user)
    {
        $cart = $this->cartManager->getRepository()->findOneBy(['sessionId' => $sessionId]);

        if ($cart) {
            $this->createOrderFromCart($cart, $user);
        }
    }

    /**
     * @return void
     */
    public function createOrderFromCart(Cart $cart, UserInterface $user)
    {
        $order = new Order();
        $this->startCreatingOrder($order, $user);

        $this->moveCartProductToOrderProduct($cart, $order);

        $this->finishCreatingOrder($cart, $order);
    }

    public function addOrderProductsFromCart(Order $order, UserInterface $user, int $cartId)
    {
        /** @var Cart $cart */
        $cart = $cart = $this->cartManager->getRepository()->find($cartId);

        if ($cart) {
            $this->startCreatingOrder($order, $user);

            $this->moveCartProductToOrderProduct($cart, $order);

            $this->calcTotalPriceForOrder($order);

            $this->finishCreatingOrder($cart, $order);
        }
    }

    /**
     * @return void
     */
    private function startCreatingOrder(Order $order, UserInterface $user)
    {
        $order
            ->setOwner($user)
            ->setStatus(OrderStaticStorage::ORDER_STATUS_CREATED)
        ;
    }

    /**
     * @return void
     */
    private function finishCreatingOrder(Cart $cart, Order $order)
    {
        $this->save($order);
        $this->remove($cart, true);
    }

    public function calcTotalPriceForOrder(Order $order): void
    {
        $orderTotalPrice = 0;

        /** @var OrderProduct $orderProduct */
        foreach ($order->getOrderProducts()->getValues() as $orderProduct) {
            $orderTotalPrice += $orderProduct->getQuantity() * $orderProduct->getPricePerOne();
        }

        $order->setTotalPrice($orderTotalPrice);
    }

    /**
     * @return void
     */
    private function moveCartProductToOrderProduct(Cart $cart, Order $order)
    {
        /** @var CartProduct $cartProduct */
        foreach ($cart->getCartProducts()->getValues() as $cartProduct) {
            $product = $cartProduct->getProduct();

            $orderProduct = new OrderProduct();
            $orderProduct
                ->setAppOrder($order)
                ->setQuantity($cartProduct->getQuantity())
                ->setProduct($product)
                ->setPricePerOne($product->getPrice())
            ;

            $order->addOrderProduct($orderProduct);
            $this->em->persist($orderProduct);
        }
    }
}
