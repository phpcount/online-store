<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\StaticStorage\OrderStaticStorage;
use App\Form\Admin\EditOrderFormType;
use App\Form\Admin\FilterType\OrderFilterFormType;
use App\Form\DTO\EditOrderModel;
use App\Form\Handler\OrderFormHandler;
use App\Utils\Manager\OrderManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/order", name="admin_order_")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function list(Request $request, OrderFormHandler $orderFormHandler): Response
    {
        $model = new EditOrderModel();

        $filterForm = $this->createForm(OrderFilterFormType::class, $model);
        $filterForm->handleRequest($request);

        $adminParams = $this->getParameter('admin');
        $pagination = $orderFormHandler->processOrderFiltersForm($request, $filterForm, $adminParams['pages']['orders']['limit']);

        $filterForm = $filterForm->createView();

        $orderStatusChoices = OrderStaticStorage::getOrderStatusChoices();

        return $this->render('admin/order/list.html.twig', compact('pagination', 'filterForm', 'orderStatusChoices'));
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @Route("/add", name="add")
     */
    public function edit(Request $request, Order $order = null, OrderFormHandler $orderFormHandler): Response
    {
        if (!$order) {
            $order = new Order();
        }

        $form = $this->createForm(EditOrderFormType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderFormHandler->processEditForm($order);

            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('admin_order_edit', ['id' => $order->getId()]);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('warning', 'Something went wrong. Please check your form');
        }

        return $this->render('admin/order/edit.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(OrderManager $orderManager, Order $order = null): Response
    {
        $date = $order->getCreatedAt()->format(DATE_RFC7231);
        $orderManager->remove($order, true);

        $this->addFlash('info', sprintf('The order from %s was successfully deleted.', $date));

        return $this->redirectToRoute('admin_order_list');
    }
}
