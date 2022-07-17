<?php

namespace App\Utils\ApiPlatform\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Cart;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SetCartSessionIdSubscriber implements EventSubscriberInterface
{
    public function setSessionId(ViewEvent $viewEvent)
    {
        $cart = $viewEvent->getControllerResult();
        $method = $viewEvent->getRequest()->getMethod();

        if (Request::METHOD_POST !== $method || !$cart instanceof Cart) {
            return;
        }

        $phpSessionId = $viewEvent->getRequest()->cookies->get('PHPSESSID');
        if (!$phpSessionId) {
            return;
        }

        $cart->setSessionId($phpSessionId);
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
                    'setSessionId', EventPriorities::PRE_WRITE,
                ],
            ],
        ];
    }
}
