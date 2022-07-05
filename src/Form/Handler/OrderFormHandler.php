<?php

namespace App\Form\Handler;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Utils\Manager\OrderManager;

class OrderFormHandler
{

    /**
     *
     * @var OrderManager
     */
    private $orderManager;

    public function __construct(OrderManager $orderManager)
    {
       $this->orderManager = $orderManager;
    }

    /**
     *
     * @param Order $order
     * @return Order
     */
    public function processEditForm(Order $order): Order
    {

        $this->orderManager->save($order);

        return $order;
    }
}