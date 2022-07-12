<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{

    /**
     *
     * @var string
     */
    private $defaultLocale;


    public function __construct(string $defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }


    /**
     *
     * @param RequestEvent $requestEvent
     * @return void
     */
    public function onKernelRequest(RequestEvent $requestEvent)
    {
        $request = $requestEvent->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        $locale = $request->get('_locale');
        if ($locale) {
            $request->getSession()->set('_locale', $locale);
        } else {
            $request->setLocale(
                $request->getSession()->get('_locale', $this->defaultLocale)
            );
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                [
                    'onKernelRequest', 20
                ]
            ],
        ];
    }
}
