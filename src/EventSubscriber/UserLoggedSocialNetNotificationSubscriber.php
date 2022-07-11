<?php

namespace App\EventSubscriber;

use App\Event\UserLoggedSocialNetEvent;
use App\Utils\Mailer\Sender\UserLoggedSocialNetEmailSender;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserLoggedSocialNetNotificationSubscriber implements EventSubscriberInterface
{
    /**
     *
     * @var UserLoggedSocialNetEmailSender
     */
    private $emailSender;

    public function __construct(UserLoggedSocialNetEmailSender $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    /**
     *
     * @param UserLoggedSocialNetEvent $event
     * @return void
     */
    public function onUserLoggedSocialNetEvent(UserLoggedSocialNetEvent $event)
    {
        $user = $event->getUser();
        $plainPassword = $event->getPlainPassword();

        $this->emailSender->sendEmailToClient($user, $plainPassword);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserLoggedSocialNetEvent::class => 'onUserLoggedSocialNetEvent',
        ];
    }
}
