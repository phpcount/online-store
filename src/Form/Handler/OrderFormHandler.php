<?php

namespace App\Form\Handler;

use App\Entity\Order;
use App\Utils\Manager\OrderManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class OrderFormHandler
{

    /**
     *
     * @var OrderManager
     */
    private $orderManager;

    /**
     *
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(OrderManager $orderManager, PaginatorInterface $paginator)
    {
       $this->orderManager = $orderManager;
       $this->paginator = $paginator;
    }

    /**
     *
     * @param Order $order
     * @return Order
     */
    public function processEditForm(Order $order): Order
    {
        $this->orderManager->calcTotalPriceForOrder($order);

        $this->orderManager->save($order);

        return $order;
    }

    public function processOrderFiltersForm(Request $request, $filterForm)
    {
        $alias = "o";
        $qb = $this->orderManager->getRepository()
            ->createQueryBuilder($alias)
            ->where("{$alias}.isDeleted = :isDeleted")
            ->setParameter("isDeleted", false)
        ;

        return $this->paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1)
        );


    }
}
