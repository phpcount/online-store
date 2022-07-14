<?php

namespace App\Form\Handler;

use App\Entity\Order;
use App\Utils\Manager\OrderManager;
use Knp\Component\Pager\PaginatorInterface;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;
use Symfony\Component\Form\FormInterface;
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

    /**
     *
     * @var FilterBuilderUpdater
     */
    private $filterBuilderUpdater;

    public function __construct(OrderManager $orderManager, PaginatorInterface $paginator, FilterBuilderUpdater $filterBuilderUpdater)
    {
       $this->orderManager = $orderManager;
       $this->paginator = $paginator;
       $this->filterBuilderUpdater = $filterBuilderUpdater;
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

    public function processOrderFiltersForm(Request $request, FormInterface $filterForm, $limit = 10)
    {
        $alias = "o";
        $qb = $this->orderManager->getRepository()
            ->createQueryBuilder($alias)
            ->leftJoin("{$alias}.owner", "u")
            ->where("{$alias}.isDeleted = :isDeleted")
            ->setParameter("isDeleted", false)
        ;

        if ($filterForm->isSubmitted()) {
            $this->filterBuilderUpdater->addFilterConditions($filterForm, $qb);
        }

        return $this->paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            $limit
        );


    }
}
