<?php

namespace App\Utils\ApiPlatform\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Order;
use App\Utils\Manager\OrderManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;
use App\Utils\Helpers\JsonHandler;
use Exception;

class MakeOrderFromCartSubscriber implements EventSubscriberInterface
{
    /**
     *
     * @var Security
     */
    private $security;

    /**
     *
     * @var OrderManager
     */
    private $orderManager;

    public function __construct(Security $security, OrderManager $orderManager)
    {
        $this->security = $security;
        $this->orderManager = $orderManager;
    }

    public function makeOrder(ViewEvent $viewEvent)
    {
        $order = $viewEvent->getControllerResult();
        $method = $viewEvent->getRequest()->getMethod();

        if (Request::METHOD_POST !== $method || !$order instanceof Order) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user) {
            return new Exception('User not found');
        }

        $contentObj = JsonHandler::parseJson($viewEvent->getRequest());
        if (!property_exists($contentObj, 'cartId')) {
            throw new Exception("Not found a need field: 'cartId'");
        }

        $this->orderManager->addOrderProductsFromCart($order, $user, $contentObj->cartId);
    }


    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array<string, mixed> The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                [
                    'makeOrder', EventPriorities::PRE_WRITE
                ]
            ]
        ];
    }
}