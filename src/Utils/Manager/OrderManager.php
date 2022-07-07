<?php

namespace App\Utils\Manager;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\StaticStorage\OrderStaticStorage;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use App\Utils\Manager\AbstractBaseManager;

class OrderManager extends AbstractBaseManager
{

    /**
     *
     * @var CartManager
     */
    private $cartManager;

    public function __construct(EntityManagerInterface $em, CartManager $cartManager)
    {
        parent::__construct($em);

        $this->cartManager = $cartManager;
    }

    /**
     *
     * @return ObjectRepository
     */
    public function getRepository(): ObjectRepository
    {
        return $this->em->getRepository(Order::class);
    }

    /**
     *
     * @param string $sessionId
     * @param UserInterface $user
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
     *
     * @param Cart $cart
     * @param UserInterface $user
     * @return void
     */
    public function createOrderFromCart(Cart $cart, UserInterface $user)
    {
        $order = new Order();
        $order
            ->setOwner($user)
            ->setStatus(OrderStaticStorage::ORDER_STATUS_CREATED)
        ;

        /** @var CartProduct $cartProduct  */
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

        $this->save($order);
        $this->remove($cart, true);
    }

    public function calcTotalPrice(Order $order): void
    {
        $orderTotalPrice = 0;

        /** @var OrderProduct $orderProduct  */
        foreach ($order->getOrderProducts()->getValues() as $orderProduct) {
            $orderTotalPrice += $orderProduct->getQuantity() * $orderProduct->getPricePerOne();
        }
        
        $order->setTotalPrice($orderTotalPrice);
    }

}
