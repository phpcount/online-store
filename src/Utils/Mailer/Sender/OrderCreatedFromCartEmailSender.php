<?php

namespace App\Utils\Mailer\Sender;

use App\Entity\Order;
use App\Utils\Mailer\DTO\MailerOptions;
use App\Utils\Mailer\MailerSender;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OrderCreatedFromCartEmailSender
{
    /**
     * @var MailerSender
     */
    private $mailerSender;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(MailerSender $mailerSender, UrlGeneratorInterface $urlGenerator)
    {
        $this->mailerSender = $mailerSender;
        $this->urlGenerator = $urlGenerator;
    }

    public function sendEmailToClient(Order $order)
    {
        $mailerOptions = new MailerOptions();

        $subject = 'OnlineStore Shop - *Thank you for your purchase!*';
        $mailerOptions
            ->setRecipient($order->getOwner()->getEmail())
            // ->setCc('manger@online-store.com')
            ->setSubject($subject)
            ->setHtmlTemplate('main/email/client/created_order_from_cart.html.twig')
            ->setContext([
                'order' => $order,
                'h1Title' => $subject,
                'infoForClient' => 'Thank you for your purchase! Our manager will contact with you in 24 hours.',
                'profileUrl' => $this->urlGenerator->generate('main_profile_index', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ])
        ;

        $this->mailerSender->sendTemplatedEmail($mailerOptions);
    }

    public function sendEmailToManager(Order $order)
    {
        $mailerOptions = new MailerOptions();

        $subject = 'Client created order.';
        $mailerOptions
            ->setRecipient($order->getOwner()->getEmail())
            ->setSubject($subject)
            ->setHtmlTemplate('main/email/manager/created_order_from_cart.html.twig')
            ->setContext([
                'order' => $order,
                'h1Title' => $subject,
            ])
        ;

        $this->mailerSender->sendTemplatedEmail($mailerOptions);
    }
}
